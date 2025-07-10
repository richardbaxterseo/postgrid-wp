<?php
/**
 * Test PostGrid Block Attributes
 * 
 * This file tests how block attributes are stored and retrieved
 */

// Add this test function to check block attributes
function postgrid_test_block_attributes() {
    // Test 1: Check how boolean attributes are handled
    $test_cases = array(
        array('showDate' => true, 'showExcerpt' => true),
        array('showDate' => false, 'showExcerpt' => false),
        array('showDate' => 'true', 'showExcerpt' => 'false'),
        array('showDate' => 1, 'showExcerpt' => 0),
        array('showDate' => '1', 'showExcerpt' => '0'),
    );
    
    echo "<h2>PostGrid Block Attribute Testing</h2>";
    
    foreach ($test_cases as $index => $test_attrs) {
        echo "<h3>Test Case " . ($index + 1) . "</h3>";
        echo "<pre>Input: " . print_r($test_attrs, true) . "</pre>";
        
        // Test wp_parse_args behavior
        $defaults = array(
            'showDate' => true,
            'showExcerpt' => true,
        );
        
        $merged = wp_parse_args($test_attrs, $defaults);
        echo "<pre>After wp_parse_args: " . print_r($merged, true) . "</pre>";
        
        // Test boolean evaluation
        echo "<p>Boolean evaluation:</p>";
        echo "<ul>";
        echo "<li>showDate evaluates to: " . ($merged['showDate'] ? 'TRUE' : 'FALSE') . "</li>";
        echo "<li>showExcerpt evaluates to: " . ($merged['showExcerpt'] ? 'TRUE' : 'FALSE') . "</li>";
        echo "</ul>";
        
        echo "<hr>";
    }
    
    // Test 2: Check actual saved block content
    echo "<h2>Actual Saved Blocks</h2>";
    
    $args = array(
        'post_type' => 'any',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_content',
                'value' => 'postgrid/postgrid',
                'compare' => 'LIKE'
            )
        )
    );
    
    $posts = get_posts($args);
    
    if (empty($posts)) {
        // Alternative method to find posts with blocks
        $all_posts = get_posts(array(
            'post_type' => 'any',
            'posts_per_page' => 20,
            'post_status' => 'publish',
        ));
        
        foreach ($all_posts as $post) {
            if (has_block('postgrid/postgrid', $post)) {
                $posts[] = $post;
            }
        }
    }
    
    foreach ($posts as $post) {
        echo "<h3>Post: " . esc_html($post->post_title) . " (ID: {$post->ID})</h3>";
        
        $blocks = parse_blocks($post->post_content);
        
        foreach ($blocks as $block) {
            if ($block['blockName'] === 'postgrid/postgrid') {
                echo "<pre>Block Attributes:\n" . print_r($block['attrs'], true) . "</pre>";
                
                // Check specific attributes
                if (isset($block['attrs']['showDate'])) {
                    $value = $block['attrs']['showDate'];
                    $type = gettype($value);
                    $bool_eval = $value ? 'TRUE' : 'FALSE';
                    echo "<p>showDate: {$value} (type: {$type}, evaluates to: {$bool_eval})</p>";
                }
                
                if (isset($block['attrs']['showExcerpt'])) {
                    $value = $block['attrs']['showExcerpt'];
                    $type = gettype($value);
                    $bool_eval = $value ? 'TRUE' : 'FALSE';
                    echo "<p>showExcerpt: {$value} (type: {$type}, evaluates to: {$bool_eval})</p>";
                }
            }
        }
    }
}

// Add to admin menu for easy testing
add_action('admin_menu', function() {
    add_management_page(
        'PostGrid Debug',
        'PostGrid Debug',
        'manage_options',
        'postgrid-debug',
        'postgrid_test_block_attributes'
    );
});
