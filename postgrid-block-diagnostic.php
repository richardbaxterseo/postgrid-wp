<?php
/**
 * PostGrid Block Diagnostic Tool
 * 
 * Place this file in your WordPress root and access it to check block registration
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this diagnostic.');
}

echo "<h1>PostGrid Block Diagnostic</h1>";

// Check if blocks are registered
$registry = WP_Block_Type_Registry::get_instance();

echo "<h2>Registered Blocks:</h2>";
echo "<h3>PostGrid Blocks:</h3>";
$postgrid_block = $registry->is_registered('postgrid/postgrid');
$caxton_block = $registry->is_registered('caxton/posts-grid');

echo "<ul>";
echo "<li>postgrid/postgrid: " . ($postgrid_block ? '✅ Registered' : '❌ NOT Registered') . "</li>";
echo "<li>caxton/posts-grid: " . ($caxton_block ? '✅ Registered' : '❌ NOT Registered') . "</li>";
echo "</ul>";

// Check plugin status
echo "<h2>Plugin Status:</h2>";
$active_plugins = get_option('active_plugins');
$is_active = in_array('postgrid/postgrid.php', $active_plugins) || in_array('caxton/postgrid.php', $active_plugins);
echo "<p>PostGrid Plugin: " . ($is_active ? '✅ Active' : '❌ Inactive') . "</p>";

// Check file structure
echo "<h2>File Structure:</h2>";
echo "<ul>";
echo "<li>Plugin Directory: " . (defined('POSTGRID_PLUGIN_DIR') ? POSTGRID_PLUGIN_DIR : 'NOT DEFINED') . "</li>";
echo "<li>block.json exists: " . (file_exists(POSTGRID_PLUGIN_DIR . 'block.json') ? '✅ Yes' : '❌ No') . "</li>";
echo "<li>build/index.js exists: " . (file_exists(POSTGRID_PLUGIN_DIR . 'build/index.js') ? '✅ Yes' : '❌ No') . "</li>";
echo "<li>src/index.js exists: " . (file_exists(POSTGRID_PLUGIN_DIR . 'src/index.js') ? '✅ Yes' : '❌ No') . "</li>";
echo "</ul>";

// Check scripts enqueued
echo "<h2>Enqueued Scripts (Editor):</h2>";
global $wp_scripts;
echo "<ul>";
foreach($wp_scripts->registered as $handle => $script) {
    if (strpos($handle, 'postgrid') !== false || strpos($script->src, 'postgrid') !== false) {
        echo "<li>" . $handle . ": " . $script->src . "</li>";
    }
}
echo "</ul>";

// Check registered block details
if ($postgrid_block) {
    $block_type = $registry->get_registered('postgrid/postgrid');
    echo "<h2>PostGrid Block Details:</h2>";
    echo "<pre>";
    echo "Editor Script: " . print_r($block_type->editor_script_handles, true) . "\n";
    echo "Editor Style: " . print_r($block_type->editor_style_handles, true) . "\n";
    echo "Style: " . print_r($block_type->style_handles, true) . "\n";
    echo "Render Callback: " . (is_callable($block_type->render_callback) ? 'Yes' : 'No') . "\n";
    echo "</pre>";
}

// Test block rendering
echo "<h2>Test Block Rendering:</h2>";
$test_attrs = array(
    'postsPerPage' => 3,
    'columns' => 3,
    'showDate' => true,
    'showExcerpt' => true
);

if ($postgrid_block) {
    $postgrid = new \PostGrid\PostGrid();
    $output = $postgrid->render_block($test_attrs);
    echo "<p>PostGrid render test: " . (empty($output) ? '❌ Empty output' : '✅ Generated output') . "</p>";
}

// Check REST API
echo "<h2>REST API Endpoint:</h2>";
$rest_url = rest_url('postgrid/v1/posts');
echo "<p>Endpoint URL: <a href='" . esc_url($rest_url) . "' target='_blank'>" . $rest_url . "</a></p>";

echo "<hr>";
echo "<p><strong>Diagnostic complete!</strong> Check the results above to identify any issues.</p>";
echo "<p>To fix block registration issues:</p>";
echo "<ol>";
echo "<li>Ensure the plugin is active</li>";
echo "<li>Clear browser cache and reload the editor</li>";
echo "<li>Check that build/index.js exists and is readable</li>";
echo "<li>Look for JavaScript errors in the browser console</li>";
echo "</ol>";
