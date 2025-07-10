# 🎯 PostGrid Feature Request: Add Category Filter to Block Settings

## Overview
Add a category filter dropdown in the PostGrid block settings panel, positioned below the "Order By" setting. This will allow users to filter posts by category directly in the block editor.

## Current Situation
- **Plugin**: PostGrid v0.1.10
- **Location**: Block Inspector Panel (right sidebar in editor)
- **Current Settings**: Posts Per Page, Order By, Order (ASC/DESC)
- **Missing**: Category filter functionality

## Requirements

### UI Placement
- Add "Category" dropdown below "Order" setting
- Include "All Categories" as default option
- Populate with all available post categories
- Match existing UI styling and spacing

### Technical Implementation

#### 1. Update Block Attributes (block.json)
```json
"selectedCategory": {
    "type": "number",
    "default": 0
}
```
*Note: This attribute already exists but isn't exposed in the UI*

#### 2. Modify Edit Component (src/edit.js)
- Import `SelectControl` from `@wordpress/components`
- Add state management for categories list
- Fetch categories using `wp.apiFetch` or `useSelect`
- Add SelectControl component after Order control

#### 3. Update REST API Query (includes/Blocks/class-block-renderer.php)
- Modify `render_callback()` to include category parameter
- Add to WP_Query args: `'cat' => $attributes['selectedCategory']`
- Ensure proper sanitization of category ID

## File Locations

### Frontend Files
- `src/edit.js` - Block editor component (needs modification)
- `src/style.scss` - Frontend styles (no changes needed)
- `block.json` - Block registration (attribute already exists)

### Backend Files
- `includes/Blocks/class-block-renderer.php` - Server-side rendering
- `includes/Api/class-rest-controller.php` - REST API endpoints

## Development Steps

### Step 1: Add Category Fetching
```javascript
// In src/edit.js, add to imports:
import { useSelect } from '@wordpress/data';

// Inside Edit component:
const categories = useSelect((select) => {
    const { getEntityRecords } = select('core');
    return getEntityRecords('taxonomy', 'category', { per_page: -1 });
}, []);
```

### Step 2: Add Category Control
```javascript
// After the Order SelectControl:
<SelectControl
    label={__('Category', 'postgrid')}
    value={attributes.selectedCategory}
    options={[
        { label: __('All Categories', 'postgrid'), value: 0 },
        ...(categories || []).map((category) => ({
            label: category.name,
            value: category.id,
        })),
    ]}
    onChange={(selectedCategory) =>
        setAttributes({ selectedCategory: parseInt(selectedCategory) })
    }
/>
```

### Step 3: Update PHP Renderer
```php
// In render_callback() method, add to query args:
if (!empty($attributes['selectedCategory'])) {
    $query_args['cat'] = absint($attributes['selectedCategory']);
}
```

## Testing Checklist

### Functionality Tests
- [ ] Category dropdown appears in block settings
- [ ] All categories load correctly
- [ ] Selecting category filters posts immediately
- [ ] "All Categories" shows posts from all categories
- [ ] Category persists after save/reload
- [ ] Works with other filters (order, posts per page)

### Edge Cases
- [ ] Sites with no categories
- [ ] Sites with 100+ categories
- [ ] Private/hidden categories
- [ ] Empty categories (no posts)
- [ ] Custom post types with custom taxonomies

### Compatibility
- [ ] Test with Gutenberg plugin active
- [ ] Test on WordPress 6.0 (minimum version)
- [ ] Test on PHP 7.4 and 8.0+
- [ ] Verify no console errors

## Build Process
```bash
# Install dependencies
npm install

# Start development
npm run start

# Build for production
npm run build

# Create release package
npm run release
```

## Code Style Guidelines
- Follow WordPress Coding Standards
- Use consistent naming (camelCase for JS, snake_case for PHP)
- Add proper JSDoc comments
- Include translatable strings with `__()` function
- Escape all output in PHP

## Gotchas & Tips

### 1. Attribute Type
The `selectedCategory` uses number type (category ID), not string. Always parseInt() the value.

### 2. REST API Permissions
Categories are public, but ensure the REST API endpoint is available and not blocked by security plugins.

### 3. Performance
Consider caching category list if site has many categories. The block already has caching via CacheManager.

### 4. Backwards Compatibility
The attribute already exists, so saved blocks won't break. They'll just show all categories until edited.

### 5. Block Validation
No need to update `save.js` as this block uses server-side rendering.

## Reference Implementation
Check similar functionality in core blocks:
- Latest Posts block (`@wordpress/block-library/src/latest-posts`)
- Query Loop block (for more complex filtering)

## Questions to Consider
1. Should subcategories be indented in the dropdown?
2. Should multiple category selection be supported?
3. Should custom taxonomies be supported?
4. Should there be an "exclude categories" option?

## Success Criteria
- Users can filter PostGrid by category
- Setting persists across saves
- No breaking changes to existing blocks
- Clean, accessible UI matching WordPress standards
- No performance regression

## Estimated Time
- Development: 2-3 hours
- Testing: 1 hour
- Total: 3-4 hours

---

**Note**: The PostGrid plugin already has the infrastructure for this feature (attribute exists, REST API supports it). The main work is exposing it in the UI and connecting the pieces.