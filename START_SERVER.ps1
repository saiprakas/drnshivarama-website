# Hospital Admin Panel Startup Script
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "Sri Krishna Hospital - Admin Panel" -ForegroundColor Green
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Host "Installing dependencies (this may take a minute)..." -ForegroundColor Yellow
    Write-Host ""
    npm install
    Write-Host ""
}

Write-Host "Starting server..." -ForegroundColor Green
Write-Host ""

# Start server
npm start
