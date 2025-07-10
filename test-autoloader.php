<?php
/**
 * Test script for PostGrid autoloader
 * Run this to verify all classes can be loaded
 */

// Define required constants
define( 'ABSPATH', true );
define( 'POSTGRID_PLUGIN_DIR', __DIR__ . '/' );

// Include the autoloader from postgrid.php
require_once __DIR__ . '/postgrid.php';

// Test classes to load
$test_classes = [
	'PostGrid\\Plugin',
	'PostGrid\\Core\\AssetManager',
	'PostGrid\\Core\\HooksManager',
	'PostGrid\\Core\\CacheManager',
	'PostGrid\\Blocks\\BlockRegistry',
	'PostGrid\\Api\\RestController',
	'PostGrid\\Compatibility\\LegacySupport',
];

echo "Testing PostGrid Autoloader\n";
echo "===========================\n\n";

$success = true;

foreach ( $test_classes as $class ) {
	echo "Testing class: {$class}... ";
	
	if ( class_exists( $class ) ) {
		echo "✓ SUCCESS\n";
	} else {
		echo "✗ FAILED\n";
		$success = false;
		
		// Try to determine the expected file path
		$relative = str_replace( 'PostGrid\\', '', $class );
		$parts = explode( '\\', $relative );
		
		if ( count( $parts ) === 1 ) {
			$file = 'includes/class-' . strtolower( str_replace( '_', '-', $parts[0] ) ) . '.php';
		} else {
			$class_name = array_pop( $parts );
			$namespace_path = implode( '/', $parts );
			$file_name = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $class_name ) );
			$file = 'includes/' . $namespace_path . '/class-' . $file_name . '.php';
		}
		
		echo "  Expected file: {$file}\n";
		echo "  File exists: " . ( file_exists( __DIR__ . '/' . $file ) ? 'Yes' : 'No' ) . "\n";
	}
}

echo "\n";
echo $success ? "All tests passed! ✓\n" : "Some tests failed! ✗\n";
