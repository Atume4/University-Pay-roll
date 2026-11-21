# University Secure Payroll and Payment Management System

**Research prototype — Laravel 13 / PHP 8.3+ / MySQL / local web browser.** Supports the design described in the research Chapter Three. It is a demonstration of a controlled workflow, **not a production banking system**.

## Start using VS Code on Windows

### A. Before starting

Install:

- PHP **8.3 or later** with `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`, `xml` enabled.
- [Composer](https://getcomposer.org/download/) and ensure `composer --version` runs in the VS Code terminal.
- MySQL 8+ (MySQL Server, XAMPP with compatible PHP, Laragon, or another local database installation).
- VS Code with a PHP extension (recommended) and the Laravel extension if available.

**Caution:** some older XAMPP distributions include PHP 8.2 or earlier; Laravel 13 requires PHP 8.3+.

### B. Extract and install

1. Extract the ZIP as a folder such as `C:\Projects\university-payroll-source`.
2. Open that folder in VS Code (`File → Open Folder`).
3. In the integrated PowerShell terminal run:

   ```powershell
   powershell -ExecutionPolicy Bypass -File .\setup-windows.ps1
   ```

   The script creates a fresh Laravel 13 application in `university-payroll`, downloads framework dependencies, and copies the supplied payroll code into it.

4. In MySQL create a new database named `university_payroll` using MySQL Workbench, phpMyAdmin, or SQL:

   ```sql
   CREATE DATABASE university_payroll CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. Open `university-payroll/.env`. Change database settings:

   ```dotenv
   APP_NAME="University Payroll Prototype"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=university_payroll
   DB_USERNAME=root
   DB_PASSWORD=YOUR_LOCAL_MYSQL_PASSWORD
   ```

   **Do not modify the generated APP_KEY after encrypting employee data.** Laravel's installer generally creates `.env` and the application key; if key missing run `php artisan key:generate` once before seeding.

6. Run in the terminal:

   ```powershell
   cd university-payroll
   php artisan migrate --seed
   php artisan serve
   ```

7. Browse to **http://127.0.0.1:8000**.

### C. Run tests

After installation, run:

```powershell
php artisan test
```

Laravel's default test suite may use SQLite. If you have no `pdo_sqlite` PHP extension, configure a separate **test-only** MySQL database before running feature tests. Never point `php artisan test` at a real payroll database. Check `php -m` for `pdo_sqlite` or `pdo_mysql`.

## Fictional login users

All demo accounts have password **`ChangeMe123!`**. These credentials are **only for a non-public local demonstration**. Do not deploy them on an exposed server.

| User role | Email | Permitted action |
|---|---|---|
| System Administrator | admin@demo.test | Create or disable user accounts |
| HR Officer | hr@demo.test | Add/edit employees and salary figures |
| Payroll Officer | payroll@demo.test | Prepare and submit a payroll batch |
| Departmental Officer | dept_officer@demo.test | Submit Works Department request |
| Departmental Approver | dept_approver@demo.test | Approve Works request |
| Finance Approver | finance_approver@demo.test | Approve payroll and finance request |
| Accounts Officer | accounts@demo.test | Export approved payroll CSV; print voucher; record external payment reference |
| Internal Auditor | auditor@demo.test | Read audit logs and permitted reports |
| Employee | employee@demo.test | View approved personal payslips |

Four **fictional** staff records and departments are created by `migrate --seed`. Bank numbers use zero-filled fictional placeholders. No Nigerian university or real staff records are provided.

## Demo the entire Chapter Three workflow

### 1. Payroll

1. Log in as **HR** to inspect/edit sample staff.
2. Sign in as **Payroll Officer**; open *Payroll cycles → New payroll*. Enter `2026-11` (or any unused YYYY-MM) and generate a draft.
3. Review salary values and select **Submit for finance approval**.
4. Sign in as **Finance Approver**; review and click **Authorize payroll**.
5. Sign in as **Accounts Officer**; open the same cycle and download **approved bank schedule CSV**. Inspect the resulting account, bank and amount columns. CSV is a **generic example**, not an actual bank's official import template.
6. After *simulated external settlement*, use Accounts to record an external reference, e.g. `SIMULATED-BANK-REF-1001`. The status means a reference was **manually entered**, not that the bank confirmed payment.
7. Sign in as **Employee** to open the approved payslip; choose browser Print → Save as PDF.

### 2. Department request

1. As **Departmental Officer**, submit `Repair faulty library lights`, `₦45,500.00`.
2. Sign in as **Departmental Approver** and approve.
3. As **Finance Approver**, approve for payment preparation.
4. As **Accounts Officer**, view printable voucher and record a **simulated** external cheque/bank reference.
5. As **Auditor**, inspect the approval history and audit log.

## Key system safeguards

- No public registration. Accounts are created by administrators only.
- Laravel session authentication with CSRF protection and throttled login attempts.
- Server-enforced roles and segregation of duties. Requesters and payroll preparers cannot authorize their own payment.
- Bank account fields and payroll bank snapshots encrypted with Laravel's `encrypted` model casts. Storage uses Laravel's `APP_KEY`; do not rotate casually.
- Staff directory shows only masked account numbers. Full account numbers are restricted to Accounts' approved CSV export and HR edit input where needed; they are never presented in audit metadata.
- Salary arithmetic is calculated using integer **kobo** to avoid floating-point rounding anomalies.
- Unique monthly payroll cycles; unique payment record per approved request/cycle.
- Transaction locks protect state transitions. Approval history and audit logs are retained.
- Plain HTML forms are protected by Blade escaping, validation and database parameter binding.

## Prototype limitations / what is not included

- This code **does not connect to banks**; cheque handling/payment settlement occurs outside the system.
- Payment success is a **user-entered reference**, not verified by a bank API.
- No internet/web hosting; run behind localhost only and do not expose `php artisan serve` to a public address.
- Exact university pension, PAYE, special allowances, tax rates, approvals limits and payroll grade rules **must be specified** before adapting the system; the prototype uses staff-level allowance/deduction inputs.
- Electronic PDF rendering uses browser printing; CSV is ready for adapting to a selected bank's approved format.
- The demo does not provide MFA, key management infrastructure, bank reconciliation, penetration testing, or production backups/DR.
- Validity, security, usability and improvement claims must be based on tests that are actually performed. The included automated tests can help gather evidence; they do not replace institutional validation.

## Research documentation

- `SYSTEM_DESIGN_MAP.md` — maps Chapter Three requirements FR1–FR12 to code and evidence.
- `docs/WORKFLOWS.md` — existing vs proposed workflow, security roles.
- `docs/TEST_CASE_CHECKLIST.md` — testing protocol and blank fields for Chapter Four measured results.
- `tests/Feature/` — test source for PHP/Laravel automated tests.

## Next implementation enhancements

After you successfully run this local prototype, replace demo salary rules with your institution's documented policies, add approved bank import templates, collect supervised usability results, and improve backup and access controls before considering real institutional deployment.
