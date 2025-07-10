<?php
/**
 * Hooks Manager
 *
 * @package PostGrid
 * @since 1.0.0
 */

namespace PostGrid\Core;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages plugin hooks and filters
 */
class HooksManager {
	
	/**
	 * Registered hooks
	 *
	 * @var array
	 */
	private $hooks = array();
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->define_hooks();
	}
	
	/**
	 * Define available hooks
	 */
	private function define_hooks() {
		// Query hooks
		$this->register_hook( 'postgrid_query_args', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_posts_query', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_post_data', 'filter', 10, 2 );
		
		// Render hooks
		$this->register_hook( 'postgrid_before_grid', 'action', 10, 1 );
		$this->register_hook( 'postgrid_after_grid', 'action', 10, 1 );
		$this->register_hook( 'postgrid_before_item', 'action', 10, 2 );
		$this->register_hook( 'postgrid_after_item', 'action', 10, 2 );
		
		// Output hooks
		$this->register_hook( 'postgrid_item_classes', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_grid_classes', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_item_title', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_item_excerpt', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_item_date_format', 'filter', 10, 1 );
		
		// Feature hooks
		$this->register_hook( 'postgrid_supported_post_types', 'filter', 10, 1 );
		$this->register_hook( 'postgrid_supported_taxonomies', 'filter', 10, 1 );
		$this->register_hook( 'postgrid_block_attributes', 'filter', 10, 1 );
		$this->register_hook( 'postgrid_rest_query_args', 'filter', 10, 1 );
		
		// Cache hooks
		$this->register_hook( 'postgrid_cache_key', 'filter', 10, 2 );
		$this->register_hook( 'postgrid_cache_expiration', 'filter', 10, 1 );
		$this->register_hook( 'postgrid_bypass_cache', 'filter', 10, 2 );
	}
	
	/**
	 * Register a hook
	 *
	 * @param string $name Hook name.
	 * @param string $type Hook type (action/filter).
	 * @param int    $priority Default priority.
	 * @param int    $args Number of arguments.
	 */
	private function register_hook( $name, $type, $priority = 10, $args = 1 ) {
		$this->hooks[ $name ] = array(
			'type' => $type,
			'priority' => $priority,
			'args' => $args,
		);
	}
	
	/**
	 * Get all registered hooks
	 *
	 * @return array
	 */
	public function get_hooks() {
		return $this->hooks;
	}
	
	/**
	 * Get hook info
	 *
	 * @param string $name Hook name.
	 * @return array|null
	 */
	public function get_hook( $name ) {
		return isset( $this->hooks[ $name ] ) ? $this->hooks[ $name ] : null;
	}
	
	/**
	 * Apply query filters
	 *
	 * @param array $args Query arguments.
	 * @param array $attributes Block attributes.
	 * @return array
	 */
	public static function apply_query_filters( $args, $attributes ) {
		return apply_filters( 'postgrid_query_args', $args, $attributes );
	}
	
	/**
	 * Apply post data filters
	 *
	 * @param array   $data Post data.
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	public static function apply_post_data_filters( $data, $post ) {
		return apply_filters( 'postgrid_post_data', $data, $post );
	}
	
	/**
	 * Get supported post types
	 *
	 * @return array
	 */
	public static function get_supported_post_types() {
		$post_types = array( 'post' );
		return apply_filters( 'postgrid_supported_post_types', $post_types );
	}
	
	/**
	 * Get supported taxonomies
	 *
	 * @return array
	 */
	public static function get_supported_taxonomies() {
		$taxonomies = array( 'category' );
		return apply_filters( 'postgrid_supported_taxonomies', $taxonomies );
	}
	
	/**
	 * Get cache key
	 *
	 * @param array $args Query arguments.
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function get_cache_key( $args, $attributes ) {
		$key = 'postgrid_' . md5( serialize( $args ) . serialize( $attributes ) );
		return apply_filters( 'postgrid_cache_key', $key, $args );
	}
	
	/**
	 * Get cache expiration
	 *
	 * @return int
	 */
	public static function get_cache_expiration() {
		return apply_filters( 'postgrid_cache_expiration', 5 * MINUTE_IN_SECONDS );
	}
	
	/**
	 * Check if should bypass cache
	 *
	 * @param array $args Query arguments.
	 * @param array $attributes Block attributes.
	 * @return bool
	 */
	public static function should_bypass_cache( $args, $attributes ) {
		$bypass = is_user_logged_in() && current_user_can( 'edit_posts' );
		return apply_filters( 'postgrid_bypass_cache', $bypass, $args );
	}
}
