#!/bin/bash
#
# PostGrid Plugin Release Script
# Creates a production-ready ZIP file with all necessary files
#

PLUGIN_SLUG="postgrid"
VERSION="0.1.8"
BUILD_DIR="./release-build"
ZIP_FILE="${PLUGIN_SLUG}-v${VERSION}.zip"

echo "Building PostGrid v${VERSION} release package..."

# Clean up any existing build directory
if [ -d "$BUILD_DIR" ]; then
    rm -rf "$BUILD_DIR"
fi

# Create build directory
mkdir -p "$BUILD_DIR/$PLUGIN_SLUG"

# Copy all necessary files
echo "Copying plugin files..."
cp -r includes "$BUILD_DIR/$PLUGIN_SLUG/"
cp -r build "$BUILD_DIR/$PLUGIN_SLUG/"
cp -r languages "$BUILD_DIR/$PLUGIN_SLUG/" 2>/dev/null || :
cp postgrid.php "$BUILD_DIR/$PLUGIN_SLUG/"
cp block.json "$BUILD_DIR/$PLUGIN_SLUG/"
cp readme.txt "$BUILD_DIR/$PLUGIN_SLUG/"
cp LICENSE "$BUILD_DIR/$PLUGIN_SLUG/" 2>/dev/null || :

# Create the ZIP file
echo "Creating ZIP file..."
cd "$BUILD_DIR"
zip -r "../$ZIP_FILE" "$PLUGIN_SLUG"
cd ..

# Clean up build directory
rm -rf "$BUILD_DIR"

echo "Release package created: $ZIP_FILE"
echo "File size: $(du -h $ZIP_FILE | cut -f1)"
echo ""
echo "This ZIP file includes all necessary files for production deployment."
