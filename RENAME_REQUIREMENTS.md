# Renaming Plugin to "PostGrid" - Requirements

## 1. File Renames
- `posts-grid-block.php` → `postgrid.php`

## 2. Text Domain Changes
Replace all instances of `posts-grid-block` with `postgrid`:
- Plugin header in main file
- `load_plugin_textdomain()` call
- All `__()` and `esc_html__()` functions
- block.json textdomain

## 3. Namespace Changes
- `PostsGridBlock` → `PostGrid`
- Update composer.json PSR-4 autoloading
- Update class file namespace

## 4. Constant Prefix Changes
- `PGB_VERSION` → `POSTGRID_VERSION`
- `PGB_PLUGIN_FILE` → `POSTGRID_PLUGIN_FILE`
- `PGB_PLUGIN_DIR` → `POSTGRID_PLUGIN_DIR`
- `PGB_PLUGIN_URL` → `POSTGRID_PLUGIN_URL`

## 5. Block Name Changes
- `posts-grid-block/posts-grid` → `postgrid/postgrid`
- Update in block.json
- Update in registerBlockType() call

## 6. REST API Namespace
- `posts-grid/v1` → `postgrid/v1`

## 7. CSS Class Names (Optional but recommended)
- `.wp-block-posts-grid` → `.wp-block-postgrid`
- `.wp-block-posts-grid__item` → `.wp-block-postgrid__item`
- `.wp-block-posts-grid__title` → `.wp-block-postgrid__title`
- `.wp-block-posts-grid__date` → `.wp-block-postgrid__date`
- `.wp-block-posts-grid__excerpt` → `.wp-block-postgrid__excerpt`

## 8. Package/Composer Updates
- Update name in package.json
- Update name in composer.json
- Update plugin name in readme.txt
