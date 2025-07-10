<?php
/**
 * Plugin Name: Posts Grid Block
 * Plugin URI: https://github.com/yourusername/posts-grid-block
 * Description: A lightweight posts grid block for WordPress
 * Author: Your Name
 * Version: 1.0.0
 * Author URI: https://yourwebsite.com/
 * Text Domain: posts-grid-block
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
define( 'PGB_VERSION', '1.0.0' );
define( 'PGB_PLUGIN_FILE', __FILE__ );
define( 'PGB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PGB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader
spl_autoload_register( function ( $class ) {
	$prefix = 'PostsGridBlock\\';
	$base_dir = PGB_PLUGIN_DIR . 'includes/';
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . 'class-posts-grid.php';

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
	load_plugin_textdomain( 'posts-grid-block', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Initialize main class
	$posts_grid = new \PostsGridBlock\PostsGrid();
	$posts_grid->init();
} );
