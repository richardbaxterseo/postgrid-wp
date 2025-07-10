<?php
/**
 * Simple Posts Grid Autoloader
 */

namespace SimplePostsGrid;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register autoloader
 */
spl_autoload_register( function ( $class ) {
	// Check if the class belongs to our namespace
	$namespace = 'SimplePostsGrid\\';
	if ( strpos( $class, $namespace ) !== 0 ) {
		return;
	}

	// Remove namespace from class name
	$class_name = str_replace( $namespace, '', $class );

	// Convert to file path
	$file_path = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );
	$file_path = strtolower( str_replace( '_', '-', $file_path ) );

	// Build the full file path
	$file = SPG_PLUGIN_DIR . 'includes/' . $file_path . '.php';

	// Load the file if it exists
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );
