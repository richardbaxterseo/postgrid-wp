# PostGrid WordPress Plugin ZIP Builder
# This script creates a clean plugin ZIP file for distribution

$pluginName = "postgrid"
$version = "0.1.1"
$sourceDir = "C:\dev\wp\caxton"
$outputDir = "C:\dev\wp\caxton\dist"
$zipName = "$pluginName-v$version.zip"

Write-Host "Building PostGrid v$version..." -ForegroundColor Green

# Create output directory if it doesn't exist
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir | Out-Null
}

# Create temporary directory for clean plugin files
$tempDir = Join-Path $env:TEMP "postgrid-build"
$pluginDir = Join-Path $tempDir $pluginName

if (Test-Path $tempDir) {
    Remove-Item -Path $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir | Out-Null
New-Item -ItemType Directory -Path $pluginDir | Out-Null

Write-Host "Copying plugin files..." -ForegroundColor Yellow

# List of files and directories to include
$includeItems = @(
    "postgrid.php",
    "block.json",
    "readme.txt",
    "license.txt",
    "includes",
    "src",
    "build",
    "assets",
    "languages"
)

# Copy files to plugin directory
foreach ($item in $includeItems) {
    $sourcePath = Join-Path $sourceDir $item
    $destPath = Join-Path $pluginDir $item
    
    if (Test-Path $sourcePath) {
        if ((Get-Item $sourcePath).PSIsContainer) {
            Copy-Item -Path $sourcePath -Destination $destPath -Recurse
            Write-Host "  Copied directory: $item" -ForegroundColor Gray
        } else {
            Copy-Item -Path $sourcePath -Destination $destPath
            Write-Host "  Copied file: $item" -ForegroundColor Gray
        }
    }
}

# Create the ZIP file
$zipPath = Join-Path $outputDir $zipName
Write-Host "`nCreating ZIP file..." -ForegroundColor Yellow

# Remove existing ZIP if it exists
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

# Create ZIP from the temp directory
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $zipPath, 'Optimal', $false)

# Clean up temp directory
Remove-Item -Path $tempDir -Recurse -Force

# Display results
$zipSize = (Get-Item $zipPath).Length / 1MB
Write-Host "`nSuccess!" -ForegroundColor Green
Write-Host "Created: $zipPath" -ForegroundColor Cyan
Write-Host "Size: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Cyan

# Open the output directory
Start-Process explorer.exe $outputDir
