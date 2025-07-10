# Changelog

All notable changes to PostGrid will be documented in this file.

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
