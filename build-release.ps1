# PostGrid Plugin Release Script for Windows
# Creates a production-ready ZIP file with all necessary files

$pluginSlug = "postgrid"
$version = "0.1.7"
$sourceDir = "C:\dev\wp\caxton"
$buildDir = "$sourceDir\release-build"
$zipFile = "$sourceDir\$pluginSlug-v$version.zip"

Write-Host "Building PostGrid v$version release package..." -ForegroundColor Green
Write-Host "Source directory: $sourceDir"

# Clean up any existing build directory
if (Test-Path $buildDir) {
    Remove-Item -Path $buildDir -Recurse -Force
}

# Create build directory
New-Item -ItemType Directory -Path "$buildDir\$pluginSlug" -Force | Out-Null

# Copy all necessary files
Write-Host "Copying plugin files..."

# Copy directories
Copy-Item -Path "$sourceDir\includes" -Destination "$buildDir\$pluginSlug\" -Recurse
Copy-Item -Path "$sourceDir\build" -Destination "$buildDir\$pluginSlug\" -Recurse

# Copy individual files
Copy-Item -Path "$sourceDir\postgrid.php" -Destination "$buildDir\$pluginSlug\"
Copy-Item -Path "$sourceDir\block.json" -Destination "$buildDir\$pluginSlug\"
Copy-Item -Path "$sourceDir\readme.txt" -Destination "$buildDir\$pluginSlug\"

# Copy optional files if they exist
if (Test-Path "$sourceDir\languages") {
    Copy-Item -Path "$sourceDir\languages" -Destination "$buildDir\$pluginSlug\" -Recurse
}
if (Test-Path "$sourceDir\LICENSE") {
    Copy-Item -Path "$sourceDir\LICENSE" -Destination "$buildDir\$pluginSlug\"
}

# Create the ZIP file
Write-Host "Creating ZIP file..."
Compress-Archive -Path "$buildDir\$pluginSlug" -DestinationPath $zipFile -Force

# Clean up build directory
Remove-Item -Path $buildDir -Recurse -Force

Write-Host "Release package created: $zipFile" -ForegroundColor Green
$size = (Get-Item $zipFile).Length / 1MB
Write-Host "File size: $([math]::Round($size, 2)) MB"
Write-Host ""
Write-Host "This ZIP file includes all necessary files for production deployment." -ForegroundColor Yellow
