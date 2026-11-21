<?php

namespace Tests\Feature;

use App\Models\FinancialRequest;
use App\Models\PayrollCycle;
use App\Models\PayrollItem;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PayrollSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $role): User
    {
        return User::where('role', $role)->firstOrFail();
    }

    public function test_bank_numbers_are_encrypted_in_database_and_masked_in_directory(): void
    {
        $staff = Staff::where('staff_number','TST001')->firstOrFail();
        $raw = DB::table('staff')->where('id',$staff->id)->value('bank_account_number');
        $this->assertNotSame('0000000001', $raw);
        $this->assertSame('0000000001', $staff->bank_account_number);
        $this->actingAs($this->user('auditor'))->get(route('staff.index'))
            ->assertOk()->assertSee('0001')->assertDontSee('0000000001');
    }

    public function test_payroll_requires_finance_approval_before_accounts_export(): void
    {
        $this->actingAs($this->user('payroll'))->post(route('payroll.store'), ['payroll_month'=>'2026-10'])
            ->assertRedirect();
        $cycle = PayrollCycle::firstOrFail();
        $this->assertCount(4, $cycle->items);
        $first = $cycle->items()->with('staff')->first();
        $this->assertSame('275000.00', $first->staff->staff_number==='TST001' ? $first->net_salary : PayrollItem::whereHas('staff',fn ($q)=>$q->where('staff_number','TST001'))->value('net_salary'));
        $this->actingAs($this->user('accounts'))->get(route('payroll.export',$cycle))->assertForbidden();
        $this->actingAs($this->user('payroll'))->post(route('payroll.submit',$cycle))->assertRedirect();
        $this->actingAs($this->user('accounts'))->get(route('payroll.export',$cycle))->assertForbidden();
        $this->actingAs($this->user('finance_approver'))->post(route('payroll.approve',$cycle))->assertRedirect();
        $this->actingAs($this->user('accounts'))->get(route('payroll.export',$cycle))->assertOk();
    }

    public function test_monthly_payroll_cannot_be_duplicated(): void
    {
        $this->actingAs($this->user('payroll'))->post(route('payroll.store'),['payroll_month'=>'2026-11'])->assertRedirect();
        $this->actingAs($this->user('payroll'))->post(route('payroll.store'),['payroll_month'=>'2026-11'])->assertSessionHasErrors('payroll_month');
        $this->assertSame(1, PayrollCycle::count());
    }

    public function test_department_request_requires_department_then_finance_approval(): void
    {
        $this->actingAs($this->user('dept_officer'))->post(route('requests.store'), [
            'title'=>'Repair plumbing', 'purpose'=>'Maintenance of campus water piping', 'amount'=>'125000.50',
        ])->assertRedirect();
        $item = FinancialRequest::firstOrFail();
        $this->actingAs($this->user('finance_approver'))->post(route('requests.finance-approve',$item))->assertStatus(409);
        $this->actingAs($this->user('accounts'))->get(route('requests.voucher',$item))->assertForbidden();
        $this->actingAs($this->user('dept_approver'))->post(route('requests.department-approve',$item))->assertRedirect();
        $this->actingAs($this->user('finance_approver'))->post(route('requests.finance-approve',$item))->assertRedirect();
        $this->assertSame('approved',$item->fresh()->status);
        $this->actingAs($this->user('accounts'))->get(route('requests.voucher',$item))->assertOk();
    }

    public function test_only_accounts_can_record_paid_status_and_only_once(): void
    {
        $this->actingAs($this->user('dept_officer'))->post(route('requests.store'), [
            'title'=>'ICT equipment','purpose'=>'Service network switches','amount'=>'40000.00',
        ]);
        $item = FinancialRequest::firstOrFail();
        $this->actingAs($this->user('dept_approver'))->post(route('requests.department-approve',$item));
        $this->actingAs($this->user('finance_approver'))->post(route('requests.finance-approve',$item));
        $payload=['method'=>'bank','external_reference'=>'DEMO-EXTERNAL-001'];
        $this->actingAs($this->user('dept_officer'))->post(route('requests.payment',$item),$payload)->assertForbidden();
        $this->actingAs($this->user('accounts'))->post(route('requests.payment',$item),$payload)->assertRedirect();
        $this->actingAs($this->user('accounts'))->post(route('requests.payment',$item),$payload)->assertStatus(409);
        $this->assertDatabaseCount('payment_records',1);
    }

    public function test_employee_cannot_access_other_employee_salary_or_staff_directory(): void
    {
        $this->actingAs($this->user('payroll'))->post(route('payroll.store'),['payroll_month'=>'2026-09']);
        $cycle = PayrollCycle::firstOrFail();
        $this->actingAs($this->user('payroll'))->post(route('payroll.submit',$cycle));
        $this->actingAs($this->user('finance_approver'))->post(route('payroll.approve',$cycle));
        $other = PayrollItem::where('staff_id','!=',$this->user('employee')->staff_id)->firstOrFail();
        $this->actingAs($this->user('employee'))->get(route('payslips.show',$other))->assertForbidden();
        $this->actingAs($this->user('employee'))->get(route('staff.index'))->assertForbidden();
        $mine = PayrollItem::where('staff_id',$this->user('employee')->staff_id)->firstOrFail();
        $this->actingAs($this->user('employee'))->get(route('payslips.show',$mine))->assertOk();
    }
}
