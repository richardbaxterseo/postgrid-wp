#!/bin/bash
# Quick plugin test script

echo "PostGrid Plugin Pre-Release Checks"
echo "================================="

# Check for namespace issues
echo "Checking for namespace issues..."
if grep -r "namespace PostGrid" --include="*.php" .; then
    echo "✓ Found PostGrid namespace"
    
    # Check for WordPress classes without backslash
    if grep -r "WP_[A-Z]" --include="*.php" . | grep -v "\\\\WP_" | grep -v "^[^:]*://"; then
        echo "⚠ WARNING: Found WordPress classes without namespace prefix"
        echo "Add backslash (\\) before these classes"
    else
        echo "✓ All WordPress classes properly prefixed"
    fi
fi

# Check PHP syntax
echo ""
echo "Checking PHP syntax..."
find . -name "*.php" -not -path "./vendor/*" -not -path "./node_modules/*" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"

# Check version consistency
echo ""
echo "Checking version consistency..."
VERSION=$(grep "Version:" postgrid.php | head -1 | sed 's/.*Version: //')
echo "Plugin version: $VERSION"

if grep -q "POSTGRID_VERSION.*$VERSION" postgrid.php; then
    echo "✓ Version constant matches"
else
    echo "✗ Version constant mismatch"
fi

if grep -q "Stable tag: $VERSION" readme.txt; then
    echo "✓ Readme stable tag matches"
else
    echo "✗ Readme stable tag mismatch"
fi

echo ""
echo "Pre-release checks complete!"
