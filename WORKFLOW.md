# PostGrid WordPress Plugin - Development Workflow

## Project Workflow Instructions

### My Preferred Development & Release Process

I work exclusively with production-ready WordPress plugin ZIP files downloaded from GitHub releases. My workflow is:

1. **Development Phase**: I make code changes locally and test them thoroughly
2. **Commit Request**: When the code is ready, I'll ask you to commit the changes with a descriptive message
3. **Version Bump**: Update the version number in all required places (plugin header, readme.txt, constants)
4. **Push & Tag**: Push to main branch and create a version tag (e.g., `v0.1.8`)
5. **Automatic Release**: GitHub Actions automatically builds a production-ready ZIP file
6. **Download & Deploy**: I download the ZIP from GitHub releases and upload to WordPress

**Important**: I never use development builds or manually created ZIPs. All deployments come from official GitHub releases to ensure consistency and traceability.

### Required Version Updates

When preparing a release, update version in these locations:
- `postgrid.php` - Plugin header "Version:" line
- `postgrid.php` - POSTGRID_VERSION constant
- `readme.txt` - "Stable tag:" line
- `block.json` - "version" field
- `CHANGELOG.md` - New version entry

### Commit Message Format

Use conventional commits:
```
type(scope): brief description

- Detailed change 1
- Detailed change 2
```

Types: feat, fix, chore, docs, style, refactor, test

### Release Process

When I say "ready to release v0.1.X":
1. Update all version references
2. Add CHANGELOG entry
3. Commit with message: `chore(release): bump version to 0.1.X`
4. Push to main
5. Create and push tag: `v0.1.X`
6. GitHub Actions creates the release automatically
7. I download `postgrid.zip` from GitHub releases

### Current Issue with v0.1.7

The v0.1.7 release may have structural issues. The plugin should be installable by:
1. Downloading `postgrid.zip` from GitHub releases
2. Uploading via WordPress Admin → Plugins → Add New → Upload Plugin
3. The ZIP should extract to `wp-content/plugins/postgrid/` with all files intact

### Notes

- Never commit `node_modules/` or `vendor/` directories
- The `build/` directory MUST be included in releases (not gitignored for releases)
- All production builds come from GitHub Actions, not local builds
