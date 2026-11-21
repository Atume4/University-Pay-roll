# Run from PowerShell after installing PHP 8.3+, Composer, and MySQL.
$ErrorActionPreference = 'Stop'
$source = Join-Path $PSScriptRoot 'project'
$target = Join-Path $PSScriptRoot 'university-payroll'
if (-not (Get-Command php -ErrorAction SilentlyContinue)) { throw 'PHP is not on PATH. Install PHP 8.3+ and restart VS Code.' }
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Composer is not installed or not on PATH.' }
if (Test-Path $target) { throw 'Folder university-payroll already exists. Move or remove it before running this script.' }
Write-Host 'Step 1: Creating Laravel 13 app...' -ForegroundColor Cyan
composer create-project laravel/laravel university-payroll '^13.0'
if ($LASTEXITCODE -ne 0) { throw 'Composer installation failed. Check PHP extensions and network access.' }
Write-Host 'Step 2: Copying payroll code...' -ForegroundColor Cyan
Get-ChildItem -LiteralPath $source -Recurse -File | ForEach-Object {
    $relative = $_.FullName.Substring($source.Length).TrimStart('\', '/')
    $out = Join-Path $target $relative
    $dir = Split-Path $out -Parent
    if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
    Copy-Item -LiteralPath $_.FullName -Destination $out -Force
}
Write-Host 'Step 3: Open the application and configure MySQL in the .env file:' -ForegroundColor Green
Write-Host $target
Write-Host 'DB_CONNECTION=mysql'; Write-Host 'DB_HOST=127.0.0.1'; Write-Host 'DB_PORT=3306'; Write-Host 'DB_DATABASE=university_payroll'; Write-Host 'DB_USERNAME=root'; Write-Host 'DB_PASSWORD=your_local_mysql_password'
Write-Host 'Then run: php artisan migrate --seed; php artisan serve'
