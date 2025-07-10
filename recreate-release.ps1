# Script to recreate v0.1.1 release with correct ZIP filename

Write-Host "Recreating PostGrid v0.1.1 release..." -ForegroundColor Green

# Delete existing v0.1.1 tag locally and remotely
Write-Host "`nDeleting existing v0.1.1 tag..." -ForegroundColor Yellow
git tag -d v0.1.1
git push origin :refs/tags/v0.1.1

# Wait for GitHub to process the deletion
Write-Host "Waiting for GitHub to process deletion..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Recreate the tag
Write-Host "`nCreating new v0.1.1 tag..." -ForegroundColor Yellow
git tag -a v0.1.1 -m "Version 0.1.1 - Critical autoloader fix"

# Push the tag to trigger GitHub Actions
Write-Host "`nPushing tag to GitHub..." -ForegroundColor Yellow
git push origin v0.1.1

Write-Host "`nDone! GitHub Actions will now create a release with postgrid.zip" -ForegroundColor Green
Write-Host "Check the Actions tab on GitHub to monitor progress." -ForegroundColor Cyan
