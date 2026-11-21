# Research objectives and functionality map

**Title:** Development of a Secured Payroll Management System for University Administration

**Scope:** Localhost proof-of-concept only. No live bank transfers, real university data, or public hosting.

| Chapter 1 / Chapter 3 objective | Functional demonstration | Evidence to collect in Chapter 4 |
|---|---|---|
| 1. Analyze existing payment workflow and requirements | `docs/WORKFLOWS.md`; role definitions; no bank integration | Requirements and existing vs proposed flow comparison |
| 2. Design/develop secured payroll and requests | Staff registration, calculation, immutable salary/bank snapshots, approval routing, CSV export, vouchers, payslips, audit log | Screenshots and design-to-implementation comparison |
| 3. Test accuracy, efficiency, security and usability | PHPUnit tests and manual test checklist | Real pass/fail results, processing time measurements and user feedback |

## Chapter 3 functional requirements mapping

| ID | Feature | Demonstration |
|---|---|---|
| FR1 | Authentication | `/login` with limited attempts, CSRF and server authorization |
| FR2 | Employee data | `/staff` create/edit by HR or admin only; masked directory accounts |
| FR3 | Payroll calculation | `Money` service in kobo; snapshots for each active staff member |
| FR4 | Payroll schedule | `/payroll`, draft → submitted → finance approved |
| FR5 | Department request | `/requests-new` created by department officer |
| FR6 | Approval workflow | Department approver → Finance approver; cannot self-approve |
| FR7 | Payment documents | Browser-printable voucher and protected CSV export |
| FR8 | Payment record | External reference recorded by Accounts once after approval |
| FR9 | Payslip | Employee-only own payslip; restricted other users |
| FR10 | Reporting/audit | `/audit` and staff/payroll/request index pages |
| FR11 | Validation | Uniqueness constraints, money limits, encrypted account and RBAC |
| FR12 | Bulk export | Approved payroll CSV (generic; adapt to the actual bank schema) |

## Where the research stops

- The demo's **payment_recorded** status means an Accounts user manually entered an external payment reference. It **does not mean that the bank confirmed a transfer**.
- The payroll CSV is a generic schedule and will require validation/adaptation before use with any named bank.
- PDF output uses browser **Print → Save as PDF** without additional packages.
- Security and processing-time claims must be based on actual test results, not inferred from code alone.
- An institution-specific tax, pension, PAYE or allowance engine has not been added: staff-level sample deductions are entered as fixed demo figures. This is deliberate because institutional payroll policies have not been supplied.
- A change of salary or bank data affects *newly generated cycles only*. Previously generated cycle snapshots do not change.
