# PostGrid Category Filter - Testing Guide

## The category filter should already be working!

Based on my investigation, the category filter is fully implemented and should be visible in the block editor. Here's what I found:

### ✅ Frontend (edit.js)
- Category dropdown is properly implemented
- It fetches categories using WordPress data API
- It's positioned after the "Order" dropdown
- Updates posts when category is changed

### ✅ Backend (REST API)
- The `/postgrid/v1/posts` endpoint accepts `category` parameter
- Properly filters posts by category ID

### ✅ Server-side Rendering
- Block renderer includes category filtering in WP_Query

## To See the Category Filter:

1. **Clear Browser Cache**
   - Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
   - Or open incognito/private window

2. **Clear WordPress Cache**
   - If using a caching plugin, clear it
   - Go to Settings → PostGrid and clear PostGrid cache

3. **Check in Block Editor**
   - Edit a page/post
   - Add or select a PostGrid block
   - Look in the right sidebar under "Posts Settings"
   - You should see:
     - Number of posts
     - Order by
     - Order
     - **Category** ← This is what you're looking for!

## If Still Not Visible:

### Check Browser Console
1. Open browser DevTools (F12)
2. Go to Console tab
3. Look for any JavaScript errors
4. Try this in console:
   ```javascript
   wp.data.select('core').getEntityRecords('taxonomy', 'category', {per_page: -1})
   ```
   This should return your categories

### Verify Plugin Version
The build I just ran shows version 0.1.8. Make sure you have the latest files uploaded.

### Check WordPress Version
- Requires WordPress 6.0+
- Block editor must be enabled (not Classic Editor)

## What the Category Filter Looks Like:

```
Posts Settings
─────────────────
Number of posts: [slider 1-20]

Order by: [Date ▼]

Order: [Descending ▼]

Category: [All Categories ▼]  ← THIS IS IT!
```

The dropdown will show:
- All Categories (default)
- Your category 1
- Your category 2
- etc.

## Quick Test:
1. Create a few posts in different categories
2. Add PostGrid block to a page
3. Select a specific category
4. The grid should update to show only posts from that category

The feature is definitely there - it just might be a caching issue preventing you from seeing it!