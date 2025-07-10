# Changelog

All notable changes to PostGrid will be documented in this file.

## [0.1.5] - 2025-01-10

### Fixed
- Fixed "Your site doesn't include support for the 'caxton/posts-grid' block" error in Gutenberg
- Created build directory with transpiled JavaScript for proper block registration
- Fixed JavaScript module loading issues by providing WordPress-compatible vanilla JS
- Improved block registration timing and error handling
- Enhanced caxton/posts-grid compatibility with proper render callbacks

### Added
- Build directory with compiled assets (index.js, style-index.css, index.css)
- Vanilla JavaScript block registration that works without build tools
- ServerSideRender support for live preview in the editor
- Enhanced diagnostic tool (postgrid-block-diagnostic.php) for troubleshooting
- Proper registration of both postgrid/postgrid and caxton/posts-grid blocks in JavaScript

### Changed
- Improved error logging for block registration failures
- Updated PostGrid class to handle missing build directories gracefully
- Enhanced legacy block support with direct instantiation

## [0.1.4] - 2025-01-10

### Fixed
- Fixed PHP Fatal error: Class "PostGrid\WP_Block_Type_Registry" not found
- Added proper namespace prefix (\) for global WordPress classes
- Corrected namespace issues in both main plugin file and PostGrid class

## [0.1.3] - 2025-01-10

### Fixed
- Fixed "caxton/posts-grid" block not found error by registering proper block aliases
- Added proper activation hook and legacy block support
- Fixed asset enqueueing with fallback logic for missing build directory
- Enhanced block registration to support both PostGrid and Caxton namespaces
- Added diagnostic tool for troubleshooting block registration issues

### Changed
- Updated block.json to reference build directory for compiled assets
- Improved error logging and handling throughout the plugin
- Enhanced PostGrid class with better style enqueueing and block compatibility
- Added support for dynamic style loading in block rendering

### Added
- Caxton block compatibility layer for seamless migration
- Block attribute conversion between Caxton and PostGrid formats
- Diagnostic tool (postgrid-diagnostic.php) for debugging

## [0.1.2] - 2025-01-10

### Fixed
- Fixed frontend CSS not loading by correcting asset paths in block.json
- Added fallback style enqueueing to ensure styles load on frontend
- Fixed block.json to reference source files directly instead of missing build files

### Changed
- Updated block.json to point to src directory for scripts and styles
- Added manual style enqueue method as backup for frontend styling

## [0.1.1] - 2025-01-10

### Fixed
- Fixed PSR-4 autoloader to properly map namespace classes to file paths
- Resolved "Class 'PostGrid\PostGrid' not found" fatal error
- Corrected plugin folder name in ZIP from 'postgrid-wp' to 'postgrid'
- Improved GitHub Actions release workflow for proper WordPress plugin structure

### Changed
- Updated build scripts to create correct plugin directory structure
- Enhanced error handling in autoloader

## [0.1.0] - 2025-01-10

### Added
- Initial release of PostGrid plugin
- Complete refactor from Caxton v1.30.1 codebase
- Simplified posts grid block with modern WordPress standards
- Legacy Caxton shortcode support for seamless migration
- Gutenberg block conversion for Caxton blocks
- Comprehensive migration guide
- PSR-4 autoloading
- Direct access prevention on all PHP files

### Features
- Responsive grid layout (1-6 columns)
- Category filtering
- Customisable post count
- Date and excerpt display toggles
- Order by date, title, or menu order
- Ascending/descending sort options
- Clean, minimal design
- No external dependencies

### Compatibility
- Full backwards compatibility with Caxton shortcodes
- Automatic attribute mapping
- Zero-downtime migration support
