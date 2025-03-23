# PowerShell deployment script for SnipSnap

# Define the source and destination directories
$sourceDir = Join-Path $PSScriptRoot "dist"
$destDir = Split-Path -Path $PSScriptRoot -Parent

# Function to build the application
function Build-App {
    Write-Host "Building application..." -ForegroundColor Cyan
    
    # Navigate to project directory and run build
    Push-Location $PSScriptRoot
    npm run build
    Pop-Location
    
    if (-not (Test-Path $sourceDir)) {
        Write-Host "Build failed: dist directory not found" -ForegroundColor Red
        exit 1
    }
}

# Function to copy files to parent directory
function Copy-BuildFiles {
    Write-Host "Copying files to deployment directory..." -ForegroundColor Cyan
    
    # Create destination directory if it doesn't exist
    if (-not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    }
    
    # Copy all files from dist to parent directory
    Copy-Item -Path "$sourceDir\*" -Destination $destDir -Recurse -Force
    
    Write-Host "Files copied successfully!" -ForegroundColor Green
}

# Main function
function Deploy-App {
    Write-Host "Starting deployment..." -ForegroundColor Cyan
    
    try {
        Build-App
        Copy-BuildFiles
        Write-Host "Deployment completed successfully!" -ForegroundColor Green
    }
    catch {
        Write-Host "Deployment failed: $_" -ForegroundColor Red
        exit 1
    }
}

# Run the deployment
Deploy-App 