# Existing vs proposed workflow

## Existing staff payroll (manual)
Staff forms and bank details → HR/Payroll compiles schedule → management approval → printed/manual bank submission → bank typing and disbursement → paper records.

## Proposed payroll (local prototype)
HR registers staff → Payroll generates month from salary and bank snapshots → Payroll submits → Finance approver authorizes → Accounts exports approved CSV → bank processing OUTSIDE prototype → Accounts enters external reference → audit entry.

## Existing departmental payments (manual)
Works/academic department paper request → officer approval → accounts types payment documents → cheque → payee visits bank → paper filing.

## Proposed departmental requests (local prototype)
Department officer enters request → department approver reviews → finance approver authorizes → Accounts opens printable voucher → external bank/cheque process OUTSIDE prototype → Accounts records external reference once → audit entry.

## Roles and controls
- Administrator: create/disable users; no right to authorize payments.
- HR: manage personnel/bank details; no payment approval.
- Payroll: generate and submit salary schedules; no finance approval.
- Departmental officer: initiate request; cannot approve.
- Departmental approver: approve/reject only own department, and not own request.
- Finance approver: approve payroll and departmental requests; cannot export or record disbursement.
- Accounts: export approved payroll; print vouchers; enter external confirmation references; cannot approve.
- Employee: only view their approved payslips.
- Internal auditor: read audit and payroll/request records; cannot change them.

## Development and evaluation
Measure salary arithmetic against expected values; timing via manual stopwatch or browser/network logs; functional/security test pass rates via recorded test cases. Preserve screenshots and measured results for Chapter 4.
