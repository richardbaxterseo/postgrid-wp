# 🐛 PostGrid Bug Fix: Category Filter Not Working

## Overview
The category filter dropdown already exists in the PostGrid block UI but is not functioning correctly. The UI element is present but the selected category isn't being applied to filter posts.

## Current Issues

### 1. Frontend (edit.js) Issues
- **Line 79**: `selectedCategory` is being passed to API incorrectly (should be `categories`)
- **Line 148**: Missing `value` prop in SelectControl
- **Line 150**: Missing `onChange` prop in SelectControl
- **Line 93**: Dependency array incomplete

### 2. Backend Issue
Need to verify the REST API endpoint accepts and processes the category parameter correctly.

## Files to Fix

### 1. src/edit.js (Immediate Fix Needed)

#### Fix #1: API Parameter (Line 79)
```javascript
// CURRENT (WRONG):
categories: selectedCategory,

// SHOULD BE:
categories: selectedCategory || undefined,
```

#### Fix #2: SelectControl Props (Lines 146-151)
```javascript
// CURRENT (BROKEN):
<SelectControl
    label={ __( 'Category', 'postgrid' ) }
    value={ selectedCategory }
    options={ categoryOptions }
    onChange={ ( value ) => setAttributes( { selectedCategory: parseInt( value ) } ) }
/>

// The actual code is missing value and onChange!
```

#### Fix #3: useEffect Dependencies (Line 93)
```javascript
// CURRENT:
}, [ postsPerPage, orderBy, order ] );

// SHOULD BE:
}, [ postsPerPage, orderBy, order, selectedCategory ] );
```

### 2. includes/Api/class-rest-controller.php

Check if the REST endpoint properly handles the category parameter:

```php
// In get_posts() method, add:
if ( ! empty( $request['categories'] ) ) {
    $args['cat'] = absint( $request['categories'] );
}
```

### 3. includes/Blocks/class-block-renderer.php

Ensure server-side rendering includes category:

```php
// In render_callback() method:
if ( ! empty( $attributes['selectedCategory'] ) ) {
    $query_args['cat'] = absint( $attributes['selectedCategory'] );
}
```

## Complete Fix Implementation

### Step 1: Fix edit.js
```javascript
// Around line 74-84, update the API call:
useEffect( () => {
    setIsLoading( true );
    
    const params = new URLSearchParams( {
        per_page: postsPerPage,
        orderby: orderBy,
        order: order,
    } );
    
    // Only add categories if one is selected
    if ( selectedCategory ) {
        params.append( 'categories', selectedCategory );
    }
    
    apiFetch( {
        path: `/postgrid/v1/posts?${params}`,
    } )
    .then( ( fetchedPosts ) => {
        setPosts( fetchedPosts );
        setIsLoading( false );
    } )
    .catch( () => {
        setPosts( [] );
        setIsLoading( false );
    } );
}, [ postsPerPage, orderBy, order, selectedCategory ] ); // Added selectedCategory
```

### Step 2: Fix the SelectControl
```javascript
<SelectControl
    label={ __( 'Category', 'postgrid' ) }
    value={ selectedCategory }
    options={ categoryOptions }
    onChange={ ( value ) => setAttributes( { selectedCategory: parseInt( value ) } ) }
/>
```

### Step 3: Verify REST API
Check `/postgrid/v1/posts` endpoint accepts `categories` parameter.

## Testing Steps

1. **npm run start** - Start development build
2. Insert PostGrid block
3. Select a category from dropdown
4. Verify posts filter immediately
5. Save post and reload - verify category persists
6. Check different categories work
7. Check "All Categories" (value: 0) shows all posts

## Quick Diagnostic

To quickly check if the issue is frontend or backend:

1. Open browser console
2. Watch Network tab
3. Change category in block
4. Check if API call includes `?categories=X`
5. Check API response

If categories param is missing → Frontend issue
If param exists but posts don't filter → Backend issue

## Root Cause

The feature was partially implemented but has bugs:
1. SelectControl is missing its value/onChange props (possible merge conflict?)
2. API parameter name mismatch
3. useEffect missing dependency

## Time Estimate

- Fix implementation: 30 minutes
- Testing: 30 minutes
- Total: 1 hour

This is a bug fix, not a new feature - the infrastructure already exists!

## Version After Fix

Bump to v0.1.11 after fixing this issue.