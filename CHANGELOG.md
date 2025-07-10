# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.6] - 2025-01-11

### Added
- Complete architecture refactoring with modular components
- PSR-4 autoloading with proper namespace structure
- Comprehensive caching system with transient and object cache support
- REST API rate limiting (60 requests/minute for non-authenticated users)
- 20+ developer hooks and filters for extensibility
- Support for custom post types via `postgrid_supported_post_types` filter
- Support for custom taxonomies via `postgrid_supported_taxonomies` filter
- Admin bar integration for cache clearing
- PHPUnit test suite with bootstrap configuration
- CSS custom properties for advanced theming
- Dark mode support with prefers-color-scheme
- Print styles for better printing experience
- Accessibility improvements with focus states
- Performance monitoring and optimization tools

### Changed
- Refactored main plugin class into separate components
- Improved asset loading with conditional enqueuing
- Enhanced security with proper permission callbacks
- Better error handling throughout the codebase
- Modernized CSS with custom properties
- Improved mobile responsive design

### Fixed
- Asset loading conflicts with block.json
- Cache invalidation on post updates
- Legacy Caxton block compatibility issues
- Memory leaks in long-running processes

### Security
- Added rate limiting to REST API endpoints
- Implemented proper CSRF protection
- Enhanced data sanitization and escaping
- Added IP-based request throttling

## [0.1.5] - 2025-01-10

### Added
- Initial release
- Ultra-lean architecture
- Modern block.json structure
- REST API endpoint
- Minimal custom CSS (under 100 lines)
- No external dependencies
- Legacy Caxton block support
