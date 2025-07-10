<?php
/**
 * Plugin Name: PostGrid
 * Plugin URI: https://github.com/yourusername/postgrid
 * Description: A lightweight posts grid block for WordPress
 * Author: Your Name
 * Version: 0.1.4
 * Author URI: https://yourwebsite.com/
 * Text Domain: postgrid
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'POSTGRID_VERSION', '0.1.4' );
define( 'POSTGRID_PLUGIN_FILE', __FILE__ );
define( 'POSTGRID_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'POSTGRID_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader
spl_autoload_register( function ( $class ) {
	$prefix = 'PostGrid\\';
	$base_dir = POSTGRID_PLUGIN_DIR . 'includes/';
	
	// Does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	// Get the relative class name
	$relative_class = substr( $class, $len );
	
	// Replace namespace separators with directory separators
	// and prefix with 'class-' and suffix with '.php'
	$class_file = 'class-' . str_replace( '\\', '-', strtolower( $relative_class ) ) . '.php';
	$file = $base_dir . $class_file;

	// If the file exists, require it
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Initialize the plugin
add_action( 'init', function() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Load text domain
	load_plugin_textdomain( 'postgrid', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Initialize main class
	$postgrid = new \PostGrid\PostGrid();
	$postgrid->init();
	
	// IMPORTANT: Register legacy support
	postgrid_register_legacy_support();
}, 5 ); // Priority 5 to ensure it runs early

// Register activation hook
register_activation_hook( __FILE__, 'postgrid_activation' );

/**
 * Plugin activation
 */
function postgrid_activation() {
	// Check if build directory exists
	if ( ! file_exists( POSTGRID_PLUGIN_DIR . 'build' ) ) {
		// Log warning about missing build files
		error_log( 'PostGrid: Build directory not found. Please run npm install && npm run build' );
	}
	
	// Flush rewrite rules
	flush_rewrite_rules();
}

/**
 * Register legacy Caxton block support
 * This ensures the caxton/posts-grid block is recognised
 */
function postgrid_register_legacy_support() {
	// Register the caxton/posts-grid block as an alias
	if ( \WP_Block_Type_Registry::get_instance()->is_registered( 'postgrid/postgrid' ) ) {
		// Get the registered PostGrid block
		$postgrid_block = \WP_Block_Type_Registry::get_instance()->get_registered( 'postgrid/postgrid' );
		
		// Register it also as caxton/posts-grid
		register_block_type( 'caxton/posts-grid', array(
			'render_callback' => array( $postgrid_block, 'render_callback' ),
			'attributes' => $postgrid_block->attributes,
			'supports' => $postgrid_block->supports,
		) );
	}
	
	// Support common Caxton block variations via shortcodes
	$caxton_blocks = array(
		'caxton/posts',
		'caxton/posts-grid', 
		'caxton/post-grid',
		'caxton/grid'
	);
	
	foreach ( $caxton_blocks as $block_name ) {
		add_shortcode( str_replace( '/', '_', $block_name ), 'postgrid_handle_legacy_caxton_shortcode' );
	}
	
	// Also support Gutenberg block comment format
	add_filter( 'render_block', 'postgrid_convert_caxton_blocks', 10, 2 );
	
	// Add filter to handle block registration
	add_filter( 'block_type_metadata', 'postgrid_filter_block_metadata', 10, 1 );
}

/**
 * Filter block metadata to support Caxton blocks
 */
function postgrid_filter_block_metadata( $metadata ) {
	// If someone is trying to use caxton/posts-grid, redirect to postgrid
	if ( isset( $metadata['name'] ) && $metadata['name'] === 'caxton/posts-grid' ) {
		$metadata['name'] = 'postgrid/postgrid';
	}
	return $metadata;
}

/**
 * Convert Caxton blocks in content
 */
function postgrid_convert_caxton_blocks( $block_content, $block ) {
	if ( isset( $block['blockName'] ) && strpos( $block['blockName'], 'caxton/' ) === 0 ) {
		// Check if it's a posts grid variant
		if ( in_array( $block['blockName'], array( 'caxton/posts-grid', 'caxton/posts', 'caxton/post-grid' ) ) ) {
			// Get PostGrid instance and render with converted attributes
			$postgrid = new \PostGrid\PostGrid();
			$converted_attrs = postgrid_convert_caxton_attributes( $block['attrs'] ?? array() );
			return $postgrid->render_block( $converted_attrs );
		}
	}
	return $block_content;
}

/**
 * Convert Caxton attributes to PostGrid format
 */
function postgrid_convert_caxton_attributes( $caxton_attrs ) {
	$postgrid_attrs = array(
		'postsPerPage' => $caxton_attrs['posts'] ?? $caxton_attrs['postsPerPage'] ?? 6,
		'orderBy' => $caxton_attrs['orderby'] ?? $caxton_attrs['orderBy'] ?? 'date',
		'order' => $caxton_attrs['order'] ?? 'desc',
		'selectedCategory' => $caxton_attrs['category'] ?? $caxton_attrs['selectedCategory'] ?? 0,
		'columns' => $caxton_attrs['columns'] ?? 3,
		'showDate' => $caxton_attrs['showDate'] ?? true,
		'showExcerpt' => $caxton_attrs['showExcerpt'] ?? true,
	);
	
	return $postgrid_attrs;
}

/**
 * Handle legacy Caxton shortcodes
 * Converts Caxton attributes to PostGrid format
 */
function postgrid_handle_legacy_caxton_shortcode( $atts ) {
	// Default to empty array if no attributes
	$atts = is_array( $atts ) ? $atts : array();
	
	// Convert attributes
	$converted_attrs = postgrid_convert_caxton_attributes( $atts );
	
	// Get PostGrid instance and render
	$postgrid = new \PostGrid\PostGrid();
	return $postgrid->render_block( $converted_attrs );
}
