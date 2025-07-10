<?php
/**
 * Plugin Name: PostGrid
 * Plugin URI: https://github.com/yourusername/postgrid
 * Description: A lightweight posts grid block for WordPress
 * Author: Your Name
 * Version: 0.1.6
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
define( 'POSTGRID_VERSION', '0.1.6' );
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
	
	// Build the file path
	$file = $base_dir . 'class-' . strtolower( str_replace( '\\', '-', $relative_class ) ) . '.php';
	
	// If the mapped file exists, require it
	if ( file_exists( $file ) ) {
		require $file;
		return;
	}
	
	// Try alternative path structure for nested namespaces
	$parts = explode( '\\', $relative_class );
	if ( count( $parts ) > 1 ) {
		$class_name = array_pop( $parts );
		$namespace_path = implode( '/', $parts );
		$file = $base_dir . $namespace_path . '/class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
		
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
} );

// Initialize the plugin
add_action( 'plugins_loaded', function() {
	// Check minimum requirements
	if ( ! postgrid_check_requirements() ) {
		return;
	}
	
	// Initialize main plugin instance
	$plugin = PostGrid\Plugin::get_instance();
	
	// Make plugin instance available globally
	$GLOBALS['postgrid'] = $plugin;
}, 5 );

// Register activation hook
register_activation_hook( __FILE__, 'postgrid_activation' );

// Register deactivation hook
register_deactivation_hook( __FILE__, 'postgrid_deactivation' );

/**
 * Check plugin requirements
 *
 * @return bool
 */
function postgrid_check_requirements() {
	// Check WordPress version
	if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) {
		add_action( 'admin_notices', 'postgrid_version_notice' );
		return false;
	}
	
	// Check PHP version
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action( 'admin_notices', 'postgrid_php_notice' );
		return false;
	}
	
	// Check if block editor is available
	if ( ! function_exists( 'register_block_type' ) ) {
		add_action( 'admin_notices', 'postgrid_blocks_notice' );
		return false;
	}
	
	return true;
}

/**
 * Plugin activation
 */
function postgrid_activation() {
	// Check if build directory exists
	if ( ! file_exists( POSTGRID_PLUGIN_DIR . 'build' ) ) {
		// Log warning about missing build files
		error_log( 'PostGrid: Build directory not found. Please run npm install && npm run build' );
	}
	
	// Create database tables if needed (for future use)
	postgrid_create_tables();
	
	// Set default options
	postgrid_set_default_options();
	
	// Flush rewrite rules
	flush_rewrite_rules();
	
	// Schedule cron events
	if ( ! wp_next_scheduled( 'postgrid_daily_cleanup' ) ) {
		wp_schedule_event( time(), 'daily', 'postgrid_daily_cleanup' );
	}
	
	// Set activation flag
	set_transient( 'postgrid_activated', true, 5 );
}

/**
 * Plugin deactivation
 */
function postgrid_deactivation() {
	// Clear scheduled events
	wp_clear_scheduled_hook( 'postgrid_daily_cleanup' );
	
	// Flush cache
	if ( class_exists( 'PostGrid\\Core\\CacheManager' ) ) {
		$cache = new PostGrid\Core\CacheManager();
		$cache->flush();
	}
	
	// Flush rewrite rules
	flush_rewrite_rules();
}

/**
 * Create database tables
 */
function postgrid_create_tables() {
	// Reserved for future use
}

/**
 * Set default options
 */
function postgrid_set_default_options() {
	$defaults = array(
		'cache_expiration' => 300,
		'enable_rest_api' => true,
		'supported_post_types' => array( 'post' ),
		'rate_limit' => 60,
	);
	
	foreach ( $defaults as $option => $value ) {
		if ( false === get_option( 'postgrid_' . $option ) ) {
			add_option( 'postgrid_' . $option, $value );
		}
	}
}

/**
 * Admin notices
 */
function postgrid_version_notice() {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'PostGrid requires WordPress 6.0 or higher. Please update WordPress to use this plugin.', 'postgrid' ); ?></p>
	</div>
	<?php
}

function postgrid_php_notice() {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'PostGrid requires PHP 7.4 or higher. Please update PHP to use this plugin.', 'postgrid' ); ?></p>
	</div>
	<?php
}

function postgrid_blocks_notice() {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'PostGrid requires the WordPress block editor. Please ensure you are using a compatible WordPress version.', 'postgrid' ); ?></p>
	</div>
	<?php
}

/**
 * Get plugin instance
 *
 * @return PostGrid\Plugin
 */
function postgrid() {
	return isset( $GLOBALS['postgrid'] ) ? $GLOBALS['postgrid'] : null;
}

/**
 * Legacy support functions
 */

/**
 * Register legacy Caxton block support
 * This ensures the caxton/posts-grid block is recognised
 */
function postgrid_register_legacy_support() {
	// This function is now handled by the LegacySupport class
	// Kept for backward compatibility
	if ( class_exists( 'PostGrid\\Compatibility\\LegacySupport' ) ) {
		$legacy = new PostGrid\Compatibility\LegacySupport();
		$legacy->init();
	}
}

/**
 * Daily cleanup cron job
 */
add_action( 'postgrid_daily_cleanup', function() {
	// Clean up old transients
	global $wpdb;
	
	$expired = $wpdb->get_col(
		"SELECT option_name FROM {$wpdb->options} 
		WHERE option_name LIKE '_transient_timeout_postgrid_%' 
		AND option_value < UNIX_TIMESTAMP()"
	);
	
	foreach ( $expired as $transient ) {
		$key = str_replace( '_transient_timeout_', '', $transient );
		delete_transient( $key );
	}
	
	// Allow other cleanup tasks
	do_action( 'postgrid_daily_cleanup' );
} );

/**
 * Handle plugin activation redirect
 */
add_action( 'admin_init', function() {
	if ( get_transient( 'postgrid_activated' ) ) {
		delete_transient( 'postgrid_activated' );
		
		if ( ! isset( $_GET['activate-multi'] ) ) {
			wp_safe_redirect( admin_url( 'options-general.php?page=postgrid-settings' ) );
			exit;
		}
	}
} );
