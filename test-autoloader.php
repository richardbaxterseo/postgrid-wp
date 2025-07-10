<?php
/**
 * PostGrid Autoloader Test
 * 
 * This script tests the autoloader functionality
 */

// Define ABSPATH for testing
define( 'ABSPATH', dirname( __FILE__ ) . '/' );

// Include the main plugin file
require_once 'postgrid.php';

// Test autoloader function
function test_autoloader() {
    $test_cases = array(
        'PostGrid\\Plugin' => 'includes/class-plugin.php',
        'PostGrid\\Core\\CacheManager' => 'includes/Core/class-cache-manager.php',
        'PostGrid\\Core\\AssetManager' => 'includes/Core/class-asset-manager.php',
        'PostGrid\\Core\\HooksManager' => 'includes/Core/class-hooks-manager.php',
        'PostGrid\\Blocks\\BlockRegistry' => 'includes/Blocks/class-block-registry.php',
        'PostGrid\\Api\\RestController' => 'includes/Api/class-rest-controller.php',
        'PostGrid\\Compatibility\\LegacySupport' => 'includes/Compatibility/class-legacy-support.php',
    );
    
    echo "Testing PostGrid Autoloader\n";
    echo "===========================\n\n";
    
    foreach ( $test_cases as $class => $expected_file ) {
        echo "Testing class: $class\n";
        echo "Expected file: $expected_file\n";
        
        // Check if file exists
        $full_path = POSTGRID_PLUGIN_DIR . $expected_file;
        if ( file_exists( $full_path ) ) {
            echo "✓ File exists\n";
        } else {
            echo "✗ File NOT found at: $full_path\n";
        }
        
        // Test if class can be loaded
        if ( class_exists( $class ) ) {
            echo "✓ Class can be loaded\n";
        } else {
            echo "✗ Class CANNOT be loaded\n";
        }
        
        echo "\n";
    }
}

// Run the test
test_autoloader();
