# PostGrid Block Settings Investigation Summary

## Issue
The "Show Date" and "Show Excerpt" settings in the PostGrid block editor weren't being reflected on the frontend, even after saving and clearing cache.

## Investigation Results

### 1. Block Structure ✅
- The block is properly configured as a dynamic block (server-side rendered)
- Attributes are correctly defined in `block.json` with boolean types and default values
- The edit.js component properly handles the toggle controls

### 2. Issues Found and Fixed

#### Cache Key Generation Issue 🔧
**Problem**: The cache key was being generated incorrectly in the block renderer.
```php
// Before (incorrect)
$cache_key = HooksManager::get_cache_key( $attributes, array() );

// After (correct) 
$cache_key = HooksManager::get_cache_key( array(), $attributes );
```
The method expects query args as the first parameter and attributes as the second.

#### Boolean Value Handling Issue 🔧
**Problem**: WordPress sometimes doesn't properly handle boolean false values in block attributes.
**Solution**: Added explicit boolean validation in `normalize_attributes()`:
```php
// Ensure boolean values are properly cast
$boolean_attrs = array('showDate', 'showExcerpt', 'showThumbnail', 'showAuthor', 'showCategories');

foreach ($boolean_attrs as $attr) {
    if (isset($attributes[$attr])) {
        $attributes[$attr] = filter_var($attributes[$attr], FILTER_VALIDATE_BOOLEAN);
    }
}
```

### 3. Rendering Path Verified ✅
- Block renderer properly checks `$attributes['showDate']` for date display
- Block renderer properly checks `$attributes['showExcerpt']` for excerpt display
- CSS styles are present and not hiding these elements

### 4. Debug Tools Created

#### `debug-attributes.php`
Tests how WordPress loads posts with PostGrid blocks and displays their attributes.

#### `postgrid-debug-test.php` 
Comprehensive test that:
- Shows how wp_parse_args handles different boolean value types
- Lists all posts with PostGrid blocks and their saved attributes
- Can be accessed via Tools > PostGrid Debug in WordPress admin

#### `test-postgrid-output.php`
Renders test blocks with different attribute combinations to verify output.

### 5. Debug Logging Added
Added extensive debug logging to track attribute values through the rendering pipeline:
- Raw attributes as received
- Normalized attributes after processing
- Individual item rendering with attribute values

## Root Causes

1. **Cache key was incorrectly generated** - This meant changes to attributes might not bust the cache properly
2. **Boolean false values need explicit handling** - WordPress block parser can return strings like "false" instead of boolean false
3. **Default values override false values** - When attributes aren't explicitly set to false in the block comment, defaults take precedence

## Testing Steps

1. Enable WP_DEBUG to see the debug logs
2. Edit a post with PostGrid block
3. Toggle "Show Date" and "Show Excerpt" to false
4. Save the post
5. View the frontend (check debug.log for attribute values)
6. Visit `/wp-content/plugins/postgrid/test-postgrid-output.php` to see test cases

## Next Steps

If the issue persists after these fixes:

1. Check if a full-page caching plugin is interfering
2. Verify the block's HTML comment in the database includes the false values
3. Test with a default theme to rule out theme conflicts
4. Check browser developer tools for any JavaScript errors during save

## Files Modified

- `includes/Blocks/class-block-renderer.php` - Fixed cache key and boolean handling
- `debug-attributes.php` - Debug tool for checking saved attributes
- `postgrid-debug-test.php` - Comprehensive attribute testing page
- `test-postgrid-output.php` - Visual block rendering tests