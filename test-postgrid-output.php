<?php
/**
 * PostGrid Block Output Test
 * 
 * Place this file in your WordPress root or plugin directory
 * and access it to see the actual rendered output
 */

// Load WordPress
require_once('../../../wp-load.php');

// Create test content with different attribute combinations
$test_blocks = array(
    // Test 1: Default (should show both)
    '<!-- wp:postgrid/postgrid /-->',
    
    // Test 2: Explicitly set to true
    '<!-- wp:postgrid/postgrid {"showDate":true,"showExcerpt":true} /-->',
    
    // Test 3: Explicitly set to false
    '<!-- wp:postgrid/postgrid {"showDate":false,"showExcerpt":false} /-->',
    
    // Test 4: Mixed
    '<!-- wp:postgrid/postgrid {"showDate":true,"showExcerpt":false} /-->',
    
    // Test 5: Only one attribute
    '<!-- wp:postgrid/postgrid {"showDate":false} /-->',
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>PostGrid Block Test</title>
    <?php wp_head(); ?>
    <style>
        .test-case {
            border: 2px solid #ddd;
            margin: 20px;
            padding: 20px;
            background: #f9f9f9;
        }
        .test-case h3 {
            margin-top: 0;
            color: #333;
        }
        .test-case pre {
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
        .rendered-output {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <h1>PostGrid Block Rendering Test</h1>
        
        <?php
        foreach ($test_blocks as $index => $block_content) {
            echo '<div class="test-case">';
            echo '<h3>Test Case ' . ($index + 1) . '</h3>';
            echo '<h4>Block Code:</h4>';
            echo '<pre>' . htmlspecialchars($block_content) . '</pre>';
            
            echo '<h4>Rendered Output:</h4>';
            echo '<div class="rendered-output">';
            
            // Render the block
            echo do_blocks($block_content);
            
            echo '</div>';
            
            // Parse and display attributes
            $blocks = parse_blocks($block_content);
            if (!empty($blocks[0]['attrs'])) {
                echo '<h4>Parsed Attributes:</h4>';
                echo '<pre>' . print_r($blocks[0]['attrs'], true) . '</pre>';
            } else {
                echo '<p><em>No attributes set (using defaults)</em></p>';
            }
            
            echo '</div>';
        }
        ?>
        
        <div class="test-case">
            <h3>Debug Information</h3>
            <p>Active Theme: <?php echo get_template(); ?></p>
            <p>PostGrid Plugin Active: <?php echo is_plugin_active('postgrid/postgrid.php') ? 'Yes' : 'No'; ?></p>
            <p>Block Registered: <?php echo WP_Block_Type_Registry::get_instance()->is_registered('postgrid/postgrid') ? 'Yes' : 'No'; ?></p>
        </div>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
