# PostGrid Release Script
# This script automates the release process for WordPress plugins
# Usage: .\release.ps1 -Version "0.1.2" -Message "Bug fixes and improvements"

param(
    [Parameter(Mandatory=$true)]
    [string]$Version,
    
    [Parameter(Mandatory=$true)]
    [string]$Message
)

# Colors for output
$InfoColor = "Cyan"
$SuccessColor = "Green"
$WarningColor = "Yellow"
$ErrorColor = "Red"

Write-Host "`n🚀 PostGrid Release Script v1.0" -ForegroundColor $InfoColor
Write-Host "================================" -ForegroundColor $InfoColor

# Validate version format
if ($Version -notmatch '^\d+\.\d+\.\d+$') {
    Write-Host "❌ Error: Version must be in format X.Y.Z (e.g., 0.1.2)" -ForegroundColor $ErrorColor
    exit 1
}

# Step 1: Update version in all files
Write-Host "`n📝 Updating version to $Version..." -ForegroundColor $WarningColor

$files = @{
    "postgrid.php" = @(
        @{Find = ' \* Version: \d+\.\d+\.\d+'; Replace = " * Version: $Version"},
        @{Find = "define\( 'POSTGRID_VERSION', '\d+\.\d+\.\d+' \);"; Replace = "define( 'POSTGRID_VERSION', '$Version' );"}
    )
    "readme.txt" = @(
        @{Find = 'Stable tag: \d+\.\d+\.\d+'; Replace = "Stable tag: $Version"}
    )
    "block.json" = @(
        @{Find = '"version": "\d+\.\d+\.\d+"'; Replace = "`"version`": `"$Version`""}
    )
}

foreach ($file in $files.Keys) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        foreach ($replacement in $files[$file]) {
            $content = $content -replace $replacement.Find, $replacement.Replace
        }
        Set-Content -Path $file -Value $content -NoNewline
        Write-Host "  ✓ Updated $file" -ForegroundColor Gray
    }
}

# Step 2: Update CHANGELOG.md
Write-Host "`n📋 Updating CHANGELOG..." -ForegroundColor $WarningColor
$date = Get-Date -Format "yyyy-MM-dd"
$changelogEntry = @"

## [$Version] - $date

### Changed
- $Message
"@

$changelog = Get-Content "CHANGELOG.md" -Raw
$changelog = $changelog -replace "(# Changelog\r?\n)", "`$1$changelogEntry`n"
Set-Content -Path "CHANGELOG.md" -Value $changelog -NoNewline
Write-Host "  ✓ Updated CHANGELOG.md" -ForegroundColor Gray

# Step 3: Commit changes
Write-Host "`n💾 Committing changes..." -ForegroundColor $WarningColor
git add .
git commit -m "chore(release): bump version to $Version`n`n- $Message"
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Changes committed" -ForegroundColor $SuccessColor
} else {
    Write-Host "  ❌ Commit failed" -ForegroundColor $ErrorColor
    exit 1
}

# Step 4: Push to main
Write-Host "`n📤 Pushing to GitHub..." -ForegroundColor $WarningColor
git push origin main
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Pushed to main branch" -ForegroundColor $SuccessColor
} else {
    Write-Host "  ❌ Push failed" -ForegroundColor $ErrorColor
    exit 1
}

# Step 5: Create and push tag
Write-Host "`n🏷️  Creating tag v$Version..." -ForegroundColor $WarningColor
git tag -a "v$Version" -m "Version $Version - $Message"
git push origin "v$Version"
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Tag created and pushed" -ForegroundColor $SuccessColor
} else {
    Write-Host "  ❌ Tag creation failed" -ForegroundColor $ErrorColor
    exit 1
}

# Step 6: Wait for GitHub Actions
Write-Host "`n⏳ GitHub Actions will now:" -ForegroundColor $InfoColor
Write-Host "  1. Create a release for v$Version" -ForegroundColor Gray
Write-Host "  2. Build the WordPress plugin ZIP" -ForegroundColor Gray
Write-Host "  3. Attach postgrid-v$Version.zip to the release" -ForegroundColor Gray

Write-Host "`n📍 Check progress at:" -ForegroundColor $InfoColor
Write-Host "  https://github.com/richardbaxterseo/postgrid-wp/actions" -ForegroundColor Gray
Write-Host "`n📦 Release will be available at:" -ForegroundColor $InfoColor
Write-Host "  https://github.com/richardbaxterseo/postgrid-wp/releases/tag/v$Version" -ForegroundColor Gray

Write-Host "`n✅ Release process initiated successfully!" -ForegroundColor $SuccessColor
Write-Host "================================`n" -ForegroundColor $InfoColor
