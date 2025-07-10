# PostGrid - Legacy Caxton Support & Migration Guide

## Overview

PostGrid includes built-in support for legacy Caxton shortcodes and blocks, making migration seamless. Your existing post grids will continue to work without modification.

## Supported Legacy Formats

### Shortcodes
The following Caxton shortcode formats are automatically supported:
- `[caxton/posts]`
- `[caxton/posts-grid]`
- `[caxton/post-grid]`
- `[caxton/grid]`

### Gutenberg Blocks
Caxton Gutenberg blocks (e.g., `<!-- wp:caxton/posts -->`) are automatically converted to PostGrid format.

## Attribute Mapping

PostGrid automatically maps common Caxton attributes:

| Caxton Attribute | PostGrid Equivalent | Notes |
|-----------------|-------------------|-------|
| `posts`, `count`, `posts_per_page` | `postsPerPage` | Number of posts to display |
| `columns`, `cols` | `columns` | Grid columns (default: 3) |
| `category`, `cat` | `selectedCategory` | Accepts ID or slug |
| `orderby` | `orderBy` | Sort field |
| `order` | `order` | ASC or DESC |
| `show_date` | `showDate` | Boolean |
| `show_excerpt` | `showExcerpt` | Boolean |

## Migration Process

### Option 1: Seamless Migration (Recommended)
1. Install PostGrid alongside Caxton
2. Test your existing post grids - they should work immediately
3. Once verified, deactivate and remove Caxton
4. Your grids continue working with PostGrid

### Option 2: Gradual Migration
1. Install PostGrid alongside Caxton
2. Update individual blocks/shortcodes to PostGrid format as needed
3. Remove Caxton once all grids are updated

## Examples

### Legacy Caxton Shortcode
```
[caxton/posts columns="4" posts="8" category="news"]
```

This automatically works with PostGrid without changes.

### New PostGrid Block (Optional)
If you want to use the new format:
```
<!-- wp:postgrid/postgrid {"columns":4,"postsPerPage":8,"selectedCategory":5} /-->
```

## Troubleshooting

### Grids Not Displaying
1. Ensure PostGrid is activated
2. Clear any caching plugins
3. Check browser console for JavaScript errors

### Category Issues
- PostGrid accepts both category IDs and slugs
- If using slugs, ensure they exist in your WordPress installation

### Styling Differences
- PostGrid uses similar CSS classes to Caxton
- Minor styling adjustments may be needed
- Custom CSS targeting `.caxton-posts-grid` should be updated to `.wp-block-postgrid`

## Advanced Migration

### Database Search & Replace (Optional)
If you want to permanently convert all Caxton blocks to PostGrid format:

```sql
-- Backup your database first!
UPDATE wp_posts 
SET post_content = REPLACE(post_content, 'wp:caxton/', 'wp:postgrid/')
WHERE post_content LIKE '%wp:caxton/%';
```

### Programmatic Conversion
```php
// Add to functions.php temporarily
add_action( 'admin_init', function() {
    if ( ! isset( $_GET['convert_caxton'] ) ) {
        return;
    }
    
    $posts = get_posts( array(
        'post_type' => 'any',
        'posts_per_page' => -1,
        's' => 'caxton/'
    ) );
    
    foreach ( $posts as $post ) {
        $content = str_replace( 'wp:caxton/', 'wp:postgrid/', $post->post_content );
        wp_update_post( array(
            'ID' => $post->ID,
            'post_content' => $content
        ) );
    }
    
    wp_die( 'Conversion complete! ' . count( $posts ) . ' posts updated.' );
} );
```

Visit `yoursite.com/wp-admin/?convert_caxton=1` to run.

## Support

If you encounter any issues with legacy Caxton content:
1. Check this guide first
2. Enable WordPress debug mode to see detailed errors
3. Report issues on our GitHub repository

## Future Compatibility

PostGrid will maintain Caxton compatibility for at least 2 years to ensure smooth transitions for all users.
