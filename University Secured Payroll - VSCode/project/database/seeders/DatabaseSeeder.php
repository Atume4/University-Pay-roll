<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'ICT' => 'Information and Communication Technology',
            'ACAD' => 'Academic Affairs',
            'WORKS' => 'Works and Maintenance',
            'BURS' => 'Bursary',
        ];
        foreach ($departments as $code => $name) Department::updateOrCreate(['code' => $code], ['name' => $name]);
        $staff = [
            ['TST001','Ada Okonkwo','ACAD','academic','CONUASS 4','245000.00','60000.00','30000.00','0000000001'],
            ['TST002','Bala Ibrahim','ICT','non_academic','CONTISS 8','155000.00','25000.00','15000.00','0000000002'],
            ['TST003','Grace Musa','WORKS','non_academic','CONTISS 6','125000.00','18500.00','11500.00','0000000003'],
            ['TST004','Peter Eze','BURS','non_academic','CONTISS 9','170000.00','30000.00','18000.00','0000000004'],
        ];
        foreach ($staff as [$number,$name,$code,$category,$grade,$basic,$allowance,$deduction,$account]) {
            Staff::updateOrCreate(['staff_number' => $number], [
                'department_id' => Department::where('code', $code)->value('id'),
                'name' => $name, 'category' => $category, 'grade' => $grade,
                'bank_name' => 'DEMO BANK – NOT REAL', 'bank_account_name' => $name,
                'bank_account_number' => $account, 'basic_salary' => $basic,
                'allowances' => $allowance, 'deductions' => $deduction, 'is_active' => true,
            ]);
        }
        $roles = [
            ['admin','System Administrator',null,null],
            ['hr','HR Officer','ACAD',null],
            ['payroll','Payroll Officer','BURS',null],
            ['dept_officer','Department Request Officer','WORKS',null],
            ['dept_approver','Department Approver','WORKS',null],
            ['finance_approver','Finance Approver','BURS',null],
            ['accounts','Accounts Officer','BURS',null],
            ['auditor','Internal Auditor',null,null],
            ['employee','Ada Okonkwo','ACAD','TST001'],
        ];
        foreach ($roles as [$role,$name,$dept,$staffNumber]) {
            User::updateOrCreate(['email' => $role.'@demo.test'], [
                'name' => $name, 'password' => 'ChangeMe123!', 'role' => $role,
                'department_id' => $dept ? Department::where('code', $dept)->value('id') : null,
                'staff_id' => $staffNumber ? Staff::where('staff_number', $staffNumber)->value('id') : null,
                'is_active' => true,
            ]);
        }
    }
}
