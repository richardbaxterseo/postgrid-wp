<?php
/**
 * Plugin Name: Simple Posts Grid
 * Plugin URI: https://github.com/yourusername/simple-posts-grid
 * Description: A secure and modern posts grid block for Gutenberg
 * Author: Your Name
 * Version: 1.0.0
 * Author URI: https://yourwebsite.com/
 * Text Domain: simple-posts-grid
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
define( 'SPG_VERSION', '1.0.0' );
define( 'SPG_PLUGIN_FILE', __FILE__ );
define( 'SPG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader
require_once SPG_PLUGIN_DIR . 'includes/autoloader.php';

// Initialize the plugin
function spg_init() {
	// Check if Gutenberg is active
	if ( ! function_exists( 'register_block_type' ) ) {
		add_action( 'admin_notices', 'spg_gutenberg_notice' );
		return;
	}

	// Load text domain
	load_plugin_textdomain( 'simple-posts-grid', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Initialize main classes
	\SimplePostsGrid\Core::instance();
}
add_action( 'plugins_loaded', 'spg_init' );

/**
 * Admin notice for Gutenberg requirement
 */
function spg_gutenberg_notice() {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'Simple Posts Grid requires the Gutenberg editor to be active.', 'simple-posts-grid' ); ?></p>
	</div>
	<?php
}
