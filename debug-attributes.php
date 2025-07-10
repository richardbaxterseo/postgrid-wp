<?php
/**
 * Debug PostGrid block attributes
 */

// Load WordPress
require_once('../../../wp-load.php');

// Get a post with PostGrid block
$args = array(
    'post_type' => 'any',
    'posts_per_page' => 10,
    'post_status' => 'publish',
);

$posts = get_posts($args);

foreach ($posts as $post) {
    if (has_block('postgrid/postgrid', $post)) {
        echo "Post ID: {$post->ID} - Title: {$post->post_title}\n";
        echo "Content:\n";
        
        $blocks = parse_blocks($post->post_content);
        
        foreach ($blocks as $block) {
            if ($block['blockName'] === 'postgrid/postgrid') {
                echo "Block attributes:\n";
                var_dump($block['attrs']);
                
                // Check specific attributes
                echo "\nshowDate: ";
                var_dump($block['attrs']['showDate'] ?? 'not set');
                
                echo "showExcerpt: ";
                var_dump($block['attrs']['showExcerpt'] ?? 'not set');
                
                echo "\n---\n";
            }
        }
    }
}
