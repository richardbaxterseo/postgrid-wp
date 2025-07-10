# PostGrid - Development Guide

## Project Overview
**Plugin Name**: PostGrid  
**Current Version**: 0.1.7  
**Purpose**: A lightweight, modern posts grid block for WordPress with no external dependencies  
**GitHub**: https://github.com/richardbaxterseo/postgrid-wp

## Plugin Architecture

### Directory Structure
```
postgrid/
├── postgrid.php                    # Main plugin file
├── readme.txt                      # WordPress.org readme
├── CHANGELOG.md                    # Version history
├── block.json                      # Block registration
├── composer.json                   # PHP dependencies
├── package.json                    # npm dependencies
├── phpcs.xml                       # Coding standards
├── build/                          # Compiled assets
│   ├── index.js                    # Block editor script
│   ├── index.css                   # Editor styles
│   ├── style-index.css             # Frontend styles
│   └── index.asset.php             # Dependencies
├── src/                            # Source files
│   ├── index.js                    # Block source
│   ├── edit.js                     # Editor component
│   ├── save.js                     # Save component
│   ├── editor.scss                 # Editor styles
│   └── style.scss                  # Frontend styles
├── includes/                       # PHP classes (PSR-4)
│   ├── class-plugin.php            # Main plugin class
│   ├── Api/
│   │   └── class-rest-controller.php
│   ├── Blocks/
│   │   ├── class-block-registry.php
│   │   └── class-block-renderer.php
│   ├── Core/
│   │   ├── class-asset-manager.php
│   │   ├── class-cache-manager.php
│   │   └── class-hooks-manager.php
│   └── Compatibility/
│       └── class-legacy-support.php
└── tests/                          # Unit tests
    ├── bootstrap.php
    └── unit/
```

## Key Components

### Main Plugin File (postgrid.php)
- Version locations (update both):
  - Plugin header: `* Version: X.Y.Z`
  - PHP constant: `define('POSTGRID_VERSION', 'X.Y.Z');`
- Custom autoloader with CamelCase to kebab-case conversion
- Composer support with fallback autoloader
- Comprehensive error handling and validation

### Plugin Class (includes/class-plugin.php)
- Singleton pattern implementation
- Component initialization with error boundaries
- Settings page integration
- Hook management

### Block Registration (block.json)
- Modern block.json v3 format
- Attributes configuration
- Asset registration
- Supports configuration

### JavaScript Architecture (src/)
- React-based block editor integration
- Modern ES6+ with WordPress dependencies
- SCSS for styling
- Build process via @wordpress/scripts

## Development Workflow

### Version Update Process
1. Update version in 4 locations:
   - `postgrid.php` (header & constant)
   - `readme.txt` (Stable tag)
   - `block.json` (version field)
   - `CHANGELOG.md` (new entry)

2. Commit with conventional format:
   ```
   chore(release): bump version to X.Y.Z
   ```

3. Create and push tag:
   ```bash
   git tag vX.Y.Z
   git push origin main --tags
   ```

### Adding New Features

#### New Block Attribute
1. Add to `block.json` attributes section
2. Update `src/edit.js` to handle attribute
3. Update `src/save.js` if needed
4. Add control in block inspector
5. Update PHP renderer if server-side rendering

#### New REST Endpoint
1. Add method to `Api\RestController`
2. Register route in `register_routes()`
3. Add permission callback
4. Implement endpoint logic
5. Add caching if appropriate

#### New Component
1. Create class file following naming convention
2. Add initialization in `Plugin::init_components()`
3. Add error handling
4. Register hooks if needed

## Block System

### Block Attributes
```json
{
  "postsPerPage": { "type": "number", "default": 6 },
  "orderBy": { "type": "string", "default": "date" },
  "order": { "type": "string", "default": "desc" },
  "selectedCategory": { "type": "number", "default": 0 },
  "columns": { "type": "number", "default": 3 },
  "showDate": { "type": "boolean", "default": true },
  "showExcerpt": { "type": "boolean", "default": true }
}
```

### Supported Features
- Wide and full alignment
- Spacing controls (margin, padding)
- No HTML editing
- Legacy Caxton block support

### REST API Endpoints
- `/wp-json/postgrid/v1/posts` - Get posts with filters
- `/wp-json/postgrid/v1/categories` - Get categories
- `/wp-json/postgrid/v1/post-types` - Get supported post types

## Technical Specifications

### Requirements
- WordPress 6.0+
- PHP 7.4+
- Block editor support

### Autoloader Logic
```php
// CamelCase to kebab-case conversion
$file_name = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $class_name ) );
```

Examples:
- `AssetManager` → `class-asset-manager.php`
- `RestController` → `class-rest-controller.php`
- `BlockRegistry` → `class-block-registry.php`

### Singleton Pattern
- Private constructor
- Clone prevention with `_doing_it_wrong()`
- Wakeup prevention with `_doing_it_wrong()`
- Static instance storage
- Lazy initialization on 'init' hook

### Error Handling
- Try-catch blocks for component initialization
- Admin notices for user-facing errors
- Error logging for debugging
- Graceful degradation
- Build file validation

## Plugin Settings

### Available Options
- `postgrid_cache_expiration` - Cache duration in seconds
- `postgrid_enable_rest_api` - Enable/disable REST API
- `postgrid_supported_post_types` - Array of post types
- `postgrid_rate_limit` - API rate limit

### Hooks & Filters

#### Actions
- `postgrid_init` - After components initialized
- `postgrid_loaded` - Plugin fully loaded
- `postgrid_ready` - After blocks registered
- `postgrid_daily_cleanup` - Daily cron job

#### Filters
- `postgrid_query_args` - Modify WP_Query args
- `postgrid_supported_post_types` - Modify post types
- `postgrid_supported_taxonomies` - Modify taxonomies
- `postgrid_cache_expiration` - Modify cache time
- `postgrid_should_load_assets` - Control asset loading

## Build Process

### Development Setup
```bash
# Install dependencies
npm install
composer install

# Start development
npm run start

# Build for production
npm run build

# Run linting
npm run lint:js
npm run lint:css
composer phpcs
```

### Asset Generation
- JavaScript compiled with webpack
- SCSS compiled to CSS
- Automatic dependency extraction
- Source maps in development

## Testing

### Unit Tests
```bash
# Run PHP tests
composer test

# Run JavaScript tests
npm run test:unit

# Run E2E tests
npm run test:e2e
```

### Manual Testing Checklist
- [ ] Block inserts correctly
- [ ] Settings save properly
- [ ] Posts display in grid
- [ ] Responsive layout works
- [ ] Category filtering works
- [ ] Pagination works
- [ ] Caching functions correctly
- [ ] Legacy blocks migrate

## Performance Considerations

### Caching Strategy
- Transient caching for queries
- Object caching support
- Cache invalidation on post updates
- Configurable cache duration

### Asset Loading
- Conditional script enqueuing
- Minimal CSS footprint
- No jQuery dependency
- Block-based asset loading

### Query Optimization
- Limited fields selection
- Proper post type filtering
- Indexed query parameters
- Pagination support

## Security Best Practices

### Direct Access Prevention
```php
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}
```

### Data Validation
- Capability checks: `current_user_can()`
- Nonce verification for forms
- Input sanitization
- Output escaping

### REST API Security
- Permission callbacks
- Rate limiting
- Input validation
- Sanitized output

## Troubleshooting

### Common Issues

#### "Class not found" Error
- Check autoloader is functioning
- Verify file naming convention
- Check namespace declarations

#### Block Not Appearing
- Verify build files exist
- Check browser console for errors
- Ensure block.json is valid

#### Styles Not Loading
- Check asset enqueueing
- Verify build process completed
- Check for CSS conflicts

### Debug Mode
Enable WordPress debug mode:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

## Legacy Support

### Caxton Block Migration
The plugin maintains compatibility with legacy Caxton blocks:
- `[caxton/posts-grid]` → `[postgrid/postgrid]`
- Automatic attribute migration
- Shortcode support maintained

### Backwards Compatibility
- Deprecated `postgrid()` function
- Legacy hooks maintained
- Old attribute names supported

## Contributing Guidelines

### Code Standards
- Follow WordPress Coding Standards
- Use PHP 7.4+ features appropriately
- Maintain PSR-4 autoloading
- Document with PHPDoc blocks

### Pull Request Process
1. Create feature branch
2. Make changes with tests
3. Run linting and tests
4. Submit PR with description
5. Address review feedback

### Commit Message Format
```
type(scope): subject

Body text explaining the change

Fixes #123
```

Types: feat, fix, docs, style, refactor, test, chore

## Deployment Checklist

### Pre-Release
- [ ] All tests passing
- [ ] Version numbers updated
- [ ] Changelog updated
- [ ] Build files generated
- [ ] No debug code remaining
- [ ] Security review completed

### Release Process
1. Create release branch
2. Update versions
3. Generate build files
4. Create tag
5. Push to repository
6. Create GitHub release
7. Deploy to WordPress.org (if applicable)

### Post-Release
- [ ] Verify GitHub release
- [ ] Test download and installation
- [ ] Monitor for issues
- [ ] Update documentation

## Resources

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [React Documentation](https://react.dev/)
- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)