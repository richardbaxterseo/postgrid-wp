# Release Notes for v0.1.0

## PostGrid v0.1.0 - Initial Release

**Release Date**: 10th July 2025

### 🎉 Introducing PostGrid

PostGrid is a lightweight WordPress posts grid block that's been completely refactored from the Caxton v1.30.1 codebase. This initial release focuses on simplicity, performance, and seamless migration from Caxton.

### ✨ Key Features

- **Clean & Minimal**: Simplified posts grid with modern WordPress standards
- **Responsive Design**: 1-6 column layouts that adapt to any screen
- **Smart Filtering**: Category selection and customisable post counts
- **Flexible Display**: Toggle dates and excerpts to suit your needs
- **Zero Dependencies**: No external libraries for maximum performance
- **PSR-4 Autoloading**: Modern PHP architecture
- **Direct Access Prevention**: Security-first approach on all PHP files

### 🔄 Legacy Caxton Support

PostGrid includes comprehensive backwards compatibility:
- Automatic recognition of Caxton shortcodes (`[caxton/posts]`, `[caxton/posts-grid]`, etc.)
- Intelligent attribute mapping
- Gutenberg block conversion via `render_block` filter
- Zero-downtime migration

### 📦 Installation

#### From GitHub Release
1. Download `postgrid-wp-v0.1.0.zip` from the releases page
2. Navigate to WordPress Admin → Plugins → Add New → Upload Plugin
3. Select the downloaded file and activate

#### From Source
1. Clone the repository: `git clone https://github.com/richardbaxterseo/postgrid-wp.git`
2. Upload to `wp-content/plugins/postgrid-wp/`
3. Activate via WordPress admin

### 🚀 Migration from Caxton

If you're currently using Caxton:
1. Install PostGrid alongside Caxton
2. Test your existing grids - they'll work immediately
3. Once verified, deactivate Caxton
4. Your grids continue working seamlessly

**Supported Caxton Formats**:
- `[caxton/posts]`
- `[caxton/posts-grid]`
- `[caxton/post-grid]`
- `[caxton/grid]`
- Gutenberg blocks: `<!-- wp:caxton/posts -->`

### 📖 Documentation

- [Migration Guide](https://github.com/richardbaxterseo/postgrid-wp/blob/main/CAXTON_MIGRATION_GUIDE.md)
- [README](https://github.com/richardbaxterseo/postgrid-wp/blob/main/README.md)

### 🛠️ Technical Details

- **WordPress Version**: 6.0+
- **PHP Version**: 7.4+
- **License**: GPL v2 or later
- **Text Domain**: postgrid

### 🤝 Contributing

PostGrid is open source! Feel free to:
- Submit issues on [GitHub](https://github.com/richardbaxterseo/postgrid-wp/issues)
- Create pull requests
- Share feedback and suggestions

### 📋 Full Changelog

**Added**
- Initial release of PostGrid plugin
- Complete refactor from Caxton v1.30.1 codebase
- Simplified posts grid block with modern WordPress standards
- Legacy Caxton shortcode support for seamless migration
- Gutenberg block conversion for Caxton blocks
- Comprehensive migration guide
- PSR-4 autoloading
- Direct access prevention on all PHP files

**Features**
- Responsive grid layout (1-6 columns)
- Category filtering
- Customisable post count
- Date and excerpt display toggles
- Order by date, title, or menu order
- Ascending/descending sort options
- Clean, minimal design
- No external dependencies

---

**Download**: [postgrid-wp-v0.1.0.zip](#)  
**Repository**: https://github.com/richardbaxterseo/postgrid-wp  
**Issues**: https://github.com/richardbaxterseo/postgrid-wp/issues
