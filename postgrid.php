<?php
/**
 * Plugin Name: PostGrid
 * Plugin URI: https://github.com/yourusername/postgrid
 * Description: A lightweight posts grid block for WordPress
 * Author: Your Name
 * Version: 0.1.1
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
define( 'POSTGRID_VERSION', '0.1.1' );
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
} );


/**
 * Register legacy Caxton shortcode support
 */
function postgrid_register_legacy_support() {
	// Support common Caxton block variations
	$caxton_blocks = array(
		'caxton/posts',
		'caxton/posts-grid', 
		'caxton/post-grid',
		'caxton/grid'
	);
	
	foreach ( $caxton_blocks as $block_name ) {
		add_shortcode( $block_name, 'postgrid_handle_legacy_caxton_shortcode' );
	}
	
	// Also support Gutenberg block comment format
	add_filter( 'render_block', 'postgrid_convert_caxton_blocks', 10, 2 );
}

/**
 * Handle legacy Caxton shortcodes
 * Converts Caxton attributes to PostGrid format
 */
function postgrid_handle_legacy_caxton_shortcode( $atts ) {
	// Default to empty array if no attributes
	$atts = is_array( $atts ) ? $atts : array();
	
	// Map common Caxton attributes to PostGrid attributes
	$mapped_atts = array();
	
	// Handle different attribute naming conventions
	if ( isset( $atts['posts'] ) ) {
		$mapped_atts['postsPerPage'] = intval( $atts['posts'] );
	} elseif ( isset( $atts['count'] ) ) {
		$mapped_atts['postsPerPage'] = intval( $atts['count'] );
	} elseif ( isset( $atts['posts_per_page'] ) ) {
		$mapped_atts['postsPerPage'] = intval( $atts['posts_per_page'] );
	}
	
	// Map columns
	if ( isset( $atts['columns'] ) ) {
		$mapped_atts['columns'] = intval( $atts['columns'] );
	} elseif ( isset( $atts['cols'] ) ) {
		$mapped_atts['columns'] = intval( $atts['cols'] );
	}
	
	// Map category
	if ( isset( $atts['category'] ) ) {
		// Handle both ID and slug
		if ( is_numeric( $atts['category'] ) ) {
			$mapped_atts['selectedCategory'] = intval( $atts['category'] );
		} else {
			$category = get_category_by_slug( $atts['category'] );
			if ( $category ) {
				$mapped_atts['selectedCategory'] = $category->term_id;
			}
		}
	} elseif ( isset( $atts['cat'] ) ) {
		$mapped_atts['selectedCategory'] = intval( $atts['cat'] );
	}
	
	// Map order settings
	if ( isset( $atts['orderby'] ) ) {
		$mapped_atts['orderBy'] = sanitize_text_field( $atts['orderby'] );
	}
	
	if ( isset( $atts['order'] ) ) {
		$mapped_atts['order'] = strtolower( $atts['order'] ) === 'asc' ? 'asc' : 'desc';
	}
	
	// Map display settings
	if ( isset( $atts['show_date'] ) ) {
		$mapped_atts['showDate'] = filter_var( $atts['show_date'], FILTER_VALIDATE_BOOLEAN );
	}
	
	if ( isset( $atts['show_excerpt'] ) ) {
		$mapped_atts['showExcerpt'] = filter_var( $atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN );
	}
	
	// Set defaults for any missing attributes
	$defaults = array(
		'postsPerPage' => 6,
		'orderBy' => 'date',
		'order' => 'desc',
		'selectedCategory' => 0,
		'columns' => 3,
		'showDate' => true,
		'showExcerpt' => true
	);
	
	$final_atts = wp_parse_args( $mapped_atts, $defaults );
	
	// Use the existing PostGrid render method
	$postgrid = new \PostGrid\PostGrid();
	return $postgrid->render_block( $final_atts );
}

/**
 * Convert Caxton Gutenberg blocks to PostGrid blocks
 */
function postgrid_convert_caxton_blocks( $block_content, $block ) {
	// Check if this is a Caxton block
	if ( strpos( $block['blockName'], 'caxton/' ) === 0 ) {
		// Create PostGrid block with converted attributes
		$postgrid = new \PostGrid\PostGrid();
		
		// Map block attributes
		$mapped_atts = array();
		$attrs = $block['attrs'] ?? array();
		
		// Similar mapping as shortcodes
		if ( isset( $attrs['posts'] ) ) {
			$mapped_atts['postsPerPage'] = intval( $attrs['posts'] );
		}
		
		if ( isset( $attrs['columns'] ) ) {
			$mapped_atts['columns'] = intval( $attrs['columns'] );
		}
		
		if ( isset( $attrs['categories'] ) && is_array( $attrs['categories'] ) && ! empty( $attrs['categories'] ) ) {
			$mapped_atts['selectedCategory'] = intval( $attrs['categories'][0] );
		}
		
		// Set defaults
		$defaults = array(
			'postsPerPage' => 6,
			'orderBy' => 'date',
			'order' => 'desc',
			'selectedCategory' => 0,
			'columns' => 3,
			'showDate' => true,
			'showExcerpt' => true
		);
		
		$final_atts = wp_parse_args( $mapped_atts, $defaults );
		
		return $postgrid->render_block( $final_atts );
	}
	
	return $block_content;
}

// Register legacy support on init
add_action( 'init', 'postgrid_register_legacy_support', 20 );
