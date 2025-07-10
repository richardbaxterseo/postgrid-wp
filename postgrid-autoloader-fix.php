<?php
/**
 * Fixed autoloader for PostGrid plugin
 * 
 * This addresses the issue where nested namespaces aren't being loaded correctly
 */

// Fixed autoloader
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
	
	// Handle nested namespaces properly
	$parts = explode( '\\', $relative_class );
	
	if ( count( $parts ) === 1 ) {
		// Top-level class (e.g., PostGrid\Plugin)
		$file = $base_dir . 'class-' . strtolower( str_replace( '_', '-', $parts[0] ) ) . '.php';
	} else {
		// Nested namespace (e.g., PostGrid\Core\AssetManager)
		$class_name = array_pop( $parts );
		$namespace_path = implode( '/', $parts );
		
		// Convert class name to file name (AssetManager -> asset-manager)
		$file_name = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $class_name ) );
		
		$file = $base_dir . $namespace_path . '/class-' . $file_name . '.php';
	}
	
	// Debug logging
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( "PostGrid Autoloader: Attempting to load class '{$class}' from file '{$file}'" );
	}
	
	// If the mapped file exists, require it
	if ( file_exists( $file ) ) {
		require_once $file;
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "PostGrid Autoloader: Successfully loaded class '{$class}'" );
		}
	} else {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "PostGrid Autoloader: Failed to find file for class '{$class}' at '{$file}'" );
		}
	}
} );
