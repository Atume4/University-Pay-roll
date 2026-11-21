UNIVERSITY SECURED PAYROLL AND PAYMENT MANAGEMENT SYSTEM — VS CODE SOURCE PACKAGE

This ZIP is a Laravel 13 source overlay, not a preinstalled vendor/ Composer bundle.
The automated setup script creates Laravel 13 and copies these project files into it.
You need internet access for Composer's initial install; after that you can run the app on localhost.

WINDOWS:
1. Install PHP 8.3+ (with pdo_mysql), Composer, and MySQL. Open VS Code.
2. Unzip the archive; open its root folder in VS Code.
3. Run: powershell -ExecutionPolicy Bypass -File .\setup-windows.ps1
4. Create the MySQL database: CREATE DATABASE university_payroll;
5. Open university-payroll/.env, set the DB_* MySQL variables.
6. cd university-payroll
7. php artisan migrate --seed
8. php artisan serve
9. Open http://127.0.0.1:8000 and use the demo accounts in project/README.md.

No public hosting or real banking connections are used.
Do not upload real staff salary or banking data to this research prototype.
