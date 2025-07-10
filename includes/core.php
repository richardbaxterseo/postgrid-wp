<?php
/**
 * Core Plugin Class
 */

namespace SimplePostsGrid;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class Core {
	/**
	 * Instance
	 *
	 * @var Core
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return Core
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Register block assets
		add_action( 'init', [ $this, 'register_block' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		
		// AJAX handler for posts
		add_action( 'wp_ajax_spg_get_posts', [ $this, 'ajax_get_posts' ] );
		add_action( 'wp_ajax_nopriv_spg_get_posts', [ $this, 'ajax_get_posts' ] );
	}

	/**
	 * Register block
	 */
	public function register_block() {
		// Register block
		register_block_type( 
			'simple-posts-grid/posts-grid',
			[
				'render_callback' => [ $this, 'render_posts_grid' ],
				'attributes' => [
					'categories' => [
						'type' => 'array',
						'default' => [],
					],
					'postsPerPage' => [
						'type' => 'number',
						'default' => 6,
					],
					'columns' => [
						'type' => 'number',
						'default' => 3,
					],
					'orderBy' => [
						'type' => 'string',
						'default' => 'date',
					],
					'order' => [
						'type' => 'string', 
						'default' => 'desc',
					],
					'displayFeaturedImage' => [
						'type' => 'boolean',
						'default' => true,
					],
					'displayTitle' => [
						'type' => 'boolean',
						'default' => true,
					],
					'displayExcerpt' => [
						'type' => 'boolean',
						'default' => true,
					],
					'displayDate' => [
						'type' => 'boolean',
						'default' => false,
					],
					'displayAuthor' => [
						'type' => 'boolean',
						'default' => false,
					],
				],
			]
		);
	}

	/**
	 * Enqueue editor assets
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'simple-posts-grid-editor',
			SPG_PLUGIN_URL . 'build/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data' ],
			SPG_VERSION,
			true
		);

		wp_localize_script( 'simple-posts-grid-editor', 'spgData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'spg_nonce' ),
		] );
		
		wp_enqueue_style(
			'simple-posts-grid-editor',
			SPG_PLUGIN_URL . 'build/editor.css',
			[ 'wp-edit-blocks' ],
			SPG_VERSION
		);
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		if ( has_block( 'simple-posts-grid/posts-grid' ) ) {
			wp_enqueue_style(
				'simple-posts-grid-frontend',
				SPG_PLUGIN_URL . 'build/style.css',
				[],
				SPG_VERSION
			);
		}
	}

	/**
	 * AJAX handler for getting posts
	 */
	public function ajax_get_posts() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'spg_nonce' ) ) {
			wp_die( esc_html__( 'Security check failed', 'simple-posts-grid' ) );
		}

		// Get and sanitize parameters
		$args = [
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 6,
			'orderby' => isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date',
			'order' => isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : 'DESC',
		];

		// Handle categories
		if ( ! empty( $_POST['categories'] ) && is_array( $_POST['categories'] ) ) {
			$args['category__in'] = array_map( 'absint', $_POST['categories'] );
		}

		// Get posts
		$posts = $this->get_posts( $args );
		
		wp_send_json_success( $posts );
	}

	/**
	 * Get posts
	 */
	private function get_posts( $args ) {
		$query = new \WP_Query( $args );
		$posts = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				
				$post_data = [
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'excerpt' => get_the_excerpt(),
					'link' => get_permalink(),
					'date' => get_the_date(),
					'author' => [
						'name' => get_the_author(),
						'link' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
					],
				];

				// Add featured image if available
				if ( has_post_thumbnail() ) {
					$post_data['featured_image'] = [
						'url' => get_the_post_thumbnail_url( null, 'large' ),
						'alt' => get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ),
					];
				}

				$posts[] = $post_data;
			}
			wp_reset_postdata();
		}

		return $posts;
	}

	/**
	 * Render posts grid block
	 */
	public function render_posts_grid( $attributes ) {
		// Prepare query args
		$args = [
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => absint( $attributes['postsPerPage'] ),
			'orderby' => sanitize_text_field( $attributes['orderBy'] ),
			'order' => sanitize_text_field( $attributes['order'] ),
		];

		// Add categories if specified
		if ( ! empty( $attributes['categories'] ) ) {
			$args['category__in'] = array_map( 'absint', $attributes['categories'] );
		}

		// Get posts
		$posts = $this->get_posts( $args );

		if ( empty( $posts ) ) {
			return '<p>' . esc_html__( 'No posts found.', 'simple-posts-grid' ) . '</p>';
		}

		// Start output
		$columns = absint( $attributes['columns'] );
		$output = '<div class="spg-posts-grid" data-columns="' . esc_attr( $columns ) . '">';

		foreach ( $posts as $post ) {
			$output .= $this->render_post_item( $post, $attributes );
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Render individual post item
	 */
	private function render_post_item( $post, $attributes ) {
		$output = '<article class="spg-post-item">';
		
		// Featured image
		if ( $attributes['displayFeaturedImage'] && isset( $post['featured_image'] ) ) {
			$output .= '<div class="spg-post-image">';
			$output .= '<a href="' . esc_url( $post['link'] ) . '">';
			$output .= '<img src="' . esc_url( $post['featured_image']['url'] ) . '" alt="' . esc_attr( $post['featured_image']['alt'] ) . '">';
			$output .= '</a>';
			$output .= '</div>';
		}

		$output .= '<div class="spg-post-content">';

		// Title
		if ( $attributes['displayTitle'] ) {
			$output .= '<h3 class="spg-post-title">';
			$output .= '<a href="' . esc_url( $post['link'] ) . '">' . esc_html( $post['title'] ) . '</a>';
			$output .= '</h3>';
		}

		// Meta
		$meta_items = [];
		
		if ( $attributes['displayDate'] ) {
			$meta_items[] = '<span class="spg-post-date">' . esc_html( $post['date'] ) . '</span>';
		}
		
		if ( $attributes['displayAuthor'] ) {
			$meta_items[] = '<span class="spg-post-author">' . 
				'<a href="' . esc_url( $post['author']['link'] ) . '">' . 
				esc_html( $post['author']['name'] ) . '</a></span>';
		}
		
		if ( ! empty( $meta_items ) ) {
			$output .= '<div class="spg-post-meta">' . implode( ' | ', $meta_items ) . '</div>';
		}

		// Excerpt
		if ( $attributes['displayExcerpt'] ) {
			$output .= '<div class="spg-post-excerpt">' . esc_html( $post['excerpt'] ) . '</div>';
		}

		$output .= '</div>'; // Close content
		$output .= '</article>';

		return $output;
	}
}
