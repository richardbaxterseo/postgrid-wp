<?php
/**
 * REST Controller
 *
 * @package PostGrid
 * @since 1.0.0
 */

namespace PostGrid\Api;

use PostGrid\Core\HooksManager;
use PostGrid\Core\CacheManager;
use WP_REST_Server;
use WP_REST_Request;
use WP_Error;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API controller
 */
class RestController {
	
	/**
	 * Namespace
	 *
	 * @var string
	 */
	private $namespace = 'postgrid/v1';
	
	/**
	 * Cache manager
	 *
	 * @var CacheManager
	 */
	private $cache;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// Get cache manager from plugin instance
		$plugin = \PostGrid\Plugin::get_instance();
		$this->cache = $plugin->get_cache_manager();
		
		// Fallback to new instance if not available
		if ( ! $this->cache ) {
			$this->cache = new CacheManager();
		}
	}
	
	/**
	 * Register REST routes
	 */
	public function register_routes() {
		// Posts endpoint
		register_rest_route( $this->namespace, '/posts', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_posts' ),
				'permission_callback' => array( $this, 'get_posts_permission' ),
				'args' => $this->get_posts_args(),
			),
		) );
		
		// Categories endpoint
		register_rest_route( $this->namespace, '/categories', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_categories' ),
				'permission_callback' => '__return_true',
				'args' => array(
					'post_type' => array(
						'type' => 'string',
						'default' => 'post',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			),
		) );
		
		// Post types endpoint
		register_rest_route( $this->namespace, '/post-types', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_post_types' ),
				'permission_callback' => array( $this, 'get_post_types_permission' ),
			),
		) );
	}
	
	/**
	 * Get posts endpoint
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_posts( $request ) {
		// Check rate limit
		if ( $this->is_rate_limited() ) {
			return new WP_Error(
				'rate_limit_exceeded',
				__( 'Rate limit exceeded. Please try again later.', 'postgrid' ),
				array( 'status' => 429 )
			);
		}
		
		// Build query args
		$args = array(
			'posts_per_page' => $request->get_param( 'per_page' ),
			'orderby' => $request->get_param( 'orderby' ),
			'order' => $request->get_param( 'order' ),
			'post_status' => 'publish',
			'post_type' => $this->validate_post_type( $request->get_param( 'post_type' ) ),
		);
		
		// Category filter
		if ( $request->get_param( 'category' ) > 0 ) {
			$args['cat'] = absint( $request->get_param( 'category' ) );
		}
		
		// Search filter
		if ( $request->get_param( 'search' ) ) {
			$args['s'] = sanitize_text_field( $request->get_param( 'search' ) );
		}
		
		// Apply filters
		$args = apply_filters( 'postgrid_rest_query_args', $args );
		
		// Check cache
		$cache_key = HooksManager::get_cache_key( $args, array( 'context' => 'rest' ) );
		$cached_data = $this->cache->get( $cache_key );
		
		if ( false !== $cached_data ) {
			return rest_ensure_response( $cached_data );
		}
		
		// Get posts
		$posts = get_posts( $args );
		$data = array();
		
		foreach ( $posts as $post ) {
			$post_data = $this->prepare_post( $post, $request );
			$data[] = HooksManager::apply_post_data_filters( $post_data, $post );
		}
		
		// Cache the results
		$this->cache->set( $cache_key, $data, HooksManager::get_cache_expiration() );
		
		return rest_ensure_response( $data );
	}
	
	/**
	 * Get categories endpoint
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_categories( $request ) {
		$post_type = $this->validate_post_type( $request->get_param( 'post_type' ) );
		$taxonomies = HooksManager::get_supported_taxonomies();
		
		$categories = array();
		
		foreach ( $taxonomies as $taxonomy ) {
			// Check if taxonomy is registered for post type
			if ( ! is_object_in_taxonomy( $post_type, $taxonomy ) ) {
				continue;
			}
			
			$terms = get_terms( array(
				'taxonomy' => $taxonomy,
				'hide_empty' => true,
			) );
			
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$categories[] = array(
						'id' => $term->term_id,
						'name' => $term->name,
						'slug' => $term->slug,
						'count' => $term->count,
						'taxonomy' => $taxonomy,
					);
				}
			}
		}
		
		return rest_ensure_response( $categories );
	}
	
	/**
	 * Get post types endpoint
	 *
	 * @return WP_REST_Response
	 */
	public function get_post_types() {
		$supported_types = HooksManager::get_supported_post_types();
		$post_types = array();
		
		foreach ( $supported_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			
			if ( $post_type_obj ) {
				$post_types[] = array(
					'slug' => $post_type,
					'name' => $post_type_obj->labels->name,
					'singular_name' => $post_type_obj->labels->singular_name,
				);
			}
		}
		
		return rest_ensure_response( $post_types );
	}
	
	/**
	 * Prepare post data
	 *
	 * @param WP_Post         $post Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return array
	 */
	private function prepare_post( $post, $request ) {
		$data = array(
			'id' => $post->ID,
			'title' => get_the_title( $post ),
			'excerpt' => get_the_excerpt( $post ),
			'link' => get_permalink( $post ),
			'date' => get_the_date( 'c', $post ),
			'date_formatted' => get_the_date( '', $post ),
		);
		
		// Add author data
		$data['author'] = array(
			'id' => $post->post_author,
			'name' => get_the_author_meta( 'display_name', $post->post_author ),
			'link' => get_author_posts_url( $post->post_author ),
		);
		
		// Add featured image
		if ( has_post_thumbnail( $post ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post );
			$data['featured_image'] = array(
				'id' => $thumbnail_id,
				'url' => get_the_post_thumbnail_url( $post, 'medium' ),
				'alt' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
			);
		}
		
		// Add categories
		$categories = get_the_category( $post->ID );
		if ( ! empty( $categories ) ) {
			$data['categories'] = array_map( function( $cat ) {
				return array(
					'id' => $cat->term_id,
					'name' => $cat->name,
					'slug' => $cat->slug,
					'link' => get_category_link( $cat ),
				);
			}, $categories );
		}
		
		return $data;
	}
	
	/**
	 * Get posts permission callback
	 *
	 * @return bool
	 */
	public function get_posts_permission() {
		// Allow public access but with rate limiting
		return true;
	}
	
	/**
	 * Get post types permission callback
	 *
	 * @return bool
	 */
	public function get_post_types_permission() {
		// Only allow for users who can edit posts
		return current_user_can( 'edit_posts' );
	}
	
	/**
	 * Validate post type
	 *
	 * @param string $post_type Post type.
	 * @return string
	 */
	private function validate_post_type( $post_type ) {
		$allowed_types = HooksManager::get_supported_post_types();
		
		if ( in_array( $post_type, $allowed_types, true ) ) {
			return $post_type;
		}
		
		return 'post';
	}
	
	/**
	 * Check if request is rate limited
	 *
	 * @return bool
	 */
	private function is_rate_limited() {
		// Skip rate limiting for logged-in users
		if ( is_user_logged_in() ) {
			return false;
		}
		
		$ip = $this->get_client_ip();
		// Get the rate limit from settings, with default of 60
		$default_limit = get_option( 'postgrid_rate_limit', 60 );
		$limit = apply_filters( 'postgrid_api_rate_limit', $default_limit );
		$window = MINUTE_IN_SECONDS;
		
		$key = 'postgrid_rate_' . $ip;
		$attempts = get_transient( $key );
		
		if ( false === $attempts ) {
			set_transient( $key, 1, $window );
			return false;
		}
		
		if ( $attempts >= $limit ) {
			return true;
		}
		
		set_transient( $key, $attempts + 1, $window );
		return false;
	}
	
	/**
	 * Get client IP
	 *
	 * @return string
	 */
	private function get_client_ip() {
		$ip_keys = array( 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' );
		
		foreach ( $ip_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				$ips = explode( ',', $_SERVER[ $key ] );
				foreach ( $ips as $ip ) {
					$ip = trim( $ip );
					
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
						return $ip;
					}
				}
			}
		}
		
		return '0.0.0.0';
	}
	
	/**
	 * Get posts endpoint args
	 *
	 * @return array
	 */
	private function get_posts_args() {
		return array(
			'per_page' => array(
				'type' => 'integer',
				'default' => 6,
				'minimum' => 1,
				'maximum' => 100,
				'sanitize_callback' => 'absint',
			),
			'orderby' => array(
				'type' => 'string',
				'default' => 'date',
				'enum' => array( 'date', 'title', 'menu_order', 'rand' ),
				'sanitize_callback' => 'sanitize_key',
			),
			'order' => array(
				'type' => 'string',
				'default' => 'desc',
				'enum' => array( 'asc', 'desc' ),
				'sanitize_callback' => 'sanitize_key',
			),
			'category' => array(
				'type' => 'integer',
				'default' => 0,
				'sanitize_callback' => 'absint',
			),
			'post_type' => array(
				'type' => 'string',
				'default' => 'post',
				'sanitize_callback' => 'sanitize_key',
			),
			'search' => array(
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}
