# Chapter Four test case and measurements checklist

**Do not claim a PASS or timing until you actually run the test.** Record machine specs, Laravel/PHP/MySQL versions, database reset state, test date, and evaluator role.

| Code | Test scenario | Expected result | Actual result | Pass / Fail | Time (sec) |
|---|---|---|---|---|---|
| F01 | Log in with valid demo credentials | Dashboard displayed | | | |
| F02 | Log in with invalid password | Authentication rejected | | | |
| F03 | Register valid staff member | Saved, account encrypted and masked | | | |
| F04 | Register invalid 9-digit account | Validation rejected | | | |
| F05 | Calculate 245000 + 60000 - 30000 | Net ₦275,000.00 | | | |
| F06 | Create payroll month twice | Duplicate rejected | | | |
| F07 | Accounts exports draft schedule | Forbidden | | | |
| F08 | Payroll officer submits cycle | State moves draft → submitted | | | |
| F09 | Finance officer approves cycle | State moves submitted → approved | | | |
| F10 | Accounts exports approved CSV | Correct bank snapshot and net amounts | | | |
| F11 | Employee views own approved payslip | Allowed | | | |
| F12 | Employee attempts another staff's payslip | Forbidden | | | |
| F13 | Department officer submits request | Pending departmental approval | | | |
| F14 | Finance tries approving without department approval | Rejected | | | |
| F15 | Approver from another department tries access | Forbidden | | | |
| F16 | Department then finance approve | State moves to approved | | | |
| F17 | Accounts prints approved voucher | Voucher accessible | | | |
| F18 | Accounts records external reference twice | Second attempt rejected | | | |
| F19 | Auditor checks approvals and audit log | Entries visible, no secrets | | | |
| F20 | Deactivated account tries to use application | Access removed | | | |

Accuracy = (Correct payroll computations / Total payroll cases) × 100.
Functional success = (Passed test cases / Total cases run) × 100.
Unauthorized access prevention = (Blocked unauthorized attempts / Total attempted) × 100.
Time reduction = ((Manual duration − Prototype duration) / Manual duration) × 100 where comparable real observations exist.

Usability should be collected with a recorded sample, predefined tasks, and a reported instrument; do not invent an 87.8% result from an unrelated publication.
