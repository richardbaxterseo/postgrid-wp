# Simple Posts Grid

A secure and modern posts grid block for WordPress Gutenberg editor.

## Features

- Secure implementation with proper sanitisation and escaping
- Modern React-based block development
- Responsive grid layout
- Customisable display options
- Category filtering
- Multiple sorting options
- Server-side rendering for better performance

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Gutenberg editor enabled

## Installation

1. Upload the plugin files to the `/wp-content/plugins/simple-posts-grid` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Posts Grid block in the Gutenberg editor

## Development

To build the plugin from source:

```bash
# Install dependencies
npm install

# Build for production
npm run build

# Start development build with watch
npm run start
```

## Security Improvements

This plugin includes several security improvements over the original Caxton plugin:

- Proper nonce verification on all AJAX requests
- Input sanitisation and validation
- Output escaping to prevent XSS
- Capability checks where appropriate
- No direct file access

## Changelog

### 1.0.0
- Initial release
- Simplified version focusing only on posts grid functionality
- Security improvements and modern code standards
