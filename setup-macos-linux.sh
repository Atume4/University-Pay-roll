#!/usr/bin/env bash
set -euo pipefail
base="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
[ ! -e "$base/university-payroll" ] || { echo 'Target exists, aborting'; exit 1; }
command -v composer >/dev/null || { echo 'Install Composer and PHP 8.3+ first'; exit 1; }
cd "$base"
composer create-project laravel/laravel university-payroll '^13.0'
cp -R project/. university-payroll/
echo 'Now edit university-payroll/.env with your MySQL settings.'
echo 'Then: cd university-payroll && php artisan migrate --seed && php artisan serve'
