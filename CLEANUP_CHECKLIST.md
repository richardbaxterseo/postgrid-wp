# Files to Remove After Testing

Once you've tested the new posts-grid-block and confirmed it's working, remove these legacy files:

## Legacy Plugin Files
- [ ] caxton.php
- [ ] caxton-main.php
- [ ] simple-posts-grid.php
- [ ] build.node.js
- [ ] webpack.config.js
- [ ] yarn.lock
- [ ] .gitignore.new
- [ ] package.json.new

## Legacy Directories
- [ ] assets/ (entire directory)
- [ ] inc/ (entire directory) 
- [ ] src/blocks/ (legacy block structure)
- [ ] includes/autoloader.php (replaced by composer)
- [ ] includes/core.php (consolidated into class-posts-grid.php)

## Build Artifacts
- [ ] build/ (will be regenerated with npm run build)

## Keep These Files
- ✅ posts-grid-block.php (new main file)
- ✅ includes/class-posts-grid.php
- ✅ src/index.js
- ✅ src/edit.js
- ✅ src/style.css
- ✅ block.json
- ✅ package.json
- ✅ composer.json
- ✅ readme.txt
- ✅ REFACTOR_SUMMARY.md

## Commands to Run After Cleanup

```bash
# Install dependencies
npm install

# Generate composer autoloader
composer install

# Build the block
npm run build
```
