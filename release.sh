#!/bin/bash
# PostGrid Release Script for Git Bash
# Usage: ./release.sh 0.1.2 "Bug fixes and improvements"

VERSION=$1
MESSAGE=$2

if [ -z "$VERSION" ] || [ -z "$MESSAGE" ]; then
    echo "Usage: ./release.sh VERSION MESSAGE"
    echo "Example: ./release.sh 0.1.2 \"Bug fixes and improvements\""
    exit 1
fi

echo "🚀 Releasing PostGrid v$VERSION..."

# Update version in files
sed -i "s/\* Version: .*/\* Version: $VERSION/" postgrid.php
sed -i "s/define( 'POSTGRID_VERSION', '.*' );/define( 'POSTGRID_VERSION', '$VERSION' );/" postgrid.php
sed -i "s/Stable tag: .*/Stable tag: $VERSION/" readme.txt
sed -i "s/\"version\": \".*\"/\"version\": \"$VERSION\"/" block.json

# Update changelog
DATE=$(date +%Y-%m-%d)
CHANGELOG_ENTRY="\n## [$VERSION] - $DATE\n\n### Changed\n- $MESSAGE\n"
sed -i "/# Changelog/a\\$CHANGELOG_ENTRY" CHANGELOG.md

# Git operations
git add .
git commit -m "chore(release): bump version to $VERSION

- $MESSAGE"
git push origin main
git tag -a "v$VERSION" -m "Version $VERSION - $MESSAGE"
git push origin "v$VERSION"

echo "✅ Release v$VERSION pushed! GitHub Actions will create the release ZIP."
echo "📦 Check: https://github.com/richardbaxterseo/postgrid-wp/releases/tag/v$VERSION"
