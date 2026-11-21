<?php
namespace App\Http\Controllers;

use App\Models\PayrollCycle;
use App\Models\PayrollItem;
use App\Models\PaymentRecord;
use App\Models\Staff;
use App\Services\Audit;
use App\Services\Money;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollController extends Controller
{
    public function index()
    {
        return view('payroll.index', ['cycles' => PayrollCycle::with('preparer')->withCount('items')->latest('payroll_month')->paginate(15)]);
    }

    public function create()
    {
        return view('payroll.create', ['activeCount' => Staff::where('is_active', true)->count()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['payroll_month'=>['required','date_format:Y-m','unique:payroll_cycles,payroll_month']]);
        $staff = Staff::where('is_active', true)->get();
        if ($staff->isEmpty()) return back()->withErrors(['payroll_month'=>'Register active staff first.']);
        // Validate and snapshot values before starting a cycle; no later bank change can alter approved exports.
        $items = [];
        foreach ($staff as $employee) {
            try { $values = Money::calculate($employee->basic_salary,$employee->allowances,$employee->deductions); }
            catch (\InvalidArgumentException $e) { throw ValidationException::withMessages(['payroll_month'=>$employee->staff_number.': '.$e->getMessage()]); }
            if (! preg_match('/^\d{10}$/D', $employee->bank_account_number)) {
                throw ValidationException::withMessages(['payroll_month'=>'Invalid bank account for '.$employee->staff_number]);
            }
            $items[] = [
                'staff_id'=>$employee->id, 'basic_salary'=>$employee->basic_salary,
                'allowances'=>$employee->allowances,'deductions'=>$employee->deductions,
                'gross_salary'=>$values['gross'],'net_salary'=>$values['net'],
                'bank_account_snapshot'=>$employee->bank_account_number,
                'bank_name_snapshot'=>$employee->bank_name,
                'account_name_snapshot'=>$employee->bank_account_name,
            ];
        }
        $cycle = DB::transaction(function () use ($data, $items) {
            $cycle = PayrollCycle::create(['payroll_month'=>$data['payroll_month'],'status'=>'draft','prepared_by'=>auth()->id()]);
            foreach ($items as $item) $cycle->items()->create($item);
            Audit::record('payroll.created','payroll',$cycle->id,['items'=>count($items)]);
            return $cycle;
        });
        return redirect()->route('payroll.show',$cycle)->with('success','Draft payroll generated for review.');
    }

    public function show(PayrollCycle $cycle)
    {
        $cycle->load(['items.staff.department','preparer','approver','paymentRecord']);
        $totalKobo = $cycle->items->sum(fn ($item) => Money::toKobo($item->net_salary));
        return view('payroll.show',['cycle'=>$cycle,'total'=>Money::fromKobo((int)$totalKobo)]);
    }

    public function submit(PayrollCycle $cycle)
    {
        DB::transaction(function () use ($cycle) {
            $locked = PayrollCycle::whereKey($cycle->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft' && $locked->prepared_by === auth()->id(),403);
            abort_unless($locked->items()->exists(),422,'No payroll items.');
            $locked->update(['status'=>'submitted']);
            Audit::record('payroll.submitted','payroll',$locked->id);
        });
        return back()->with('success','Payroll submitted for finance approval.');
    }

    public function approve(PayrollCycle $cycle)
    {
        DB::transaction(function () use ($cycle) {
            $locked = PayrollCycle::whereKey($cycle->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'submitted',409,'Only submitted payroll can be approved.');
            abort_if($locked->prepared_by === auth()->id(),403,'The preparer cannot approve the payroll.');
            $locked->update(['status'=>'approved','approved_by'=>auth()->id(),'approved_at'=>now()]);
            Audit::record('payroll.approved','payroll',$locked->id);
        });
        return back()->with('success','Payroll approved. Accounts can now export its schedule.');
    }

    public function export(PayrollCycle $cycle)
    {
        abort_unless(in_array($cycle->status,['approved','payment_recorded'],true),403,'Approval required before export.');
        $cycle->load('items.staff');
        Audit::record('payroll.bank_csv_exported','payroll',$cycle->id);
        $safeCsv = static function (string $value): string {
            // Defend against spreadsheet formula injection in attacker-controlled text.
            return preg_match('/^[=+\-@\t\r\n]/u', $value) ? "'".$value : $value;
        };
        return response()->streamDownload(function () use ($cycle,$safeCsv) {
            $output = fopen('php://output','w');
            fputcsv($output, ['Staff Number','Account Name','Bank','Account Number','Amount NGN','Narration'], ',', '"', '');
            foreach ($cycle->items as $item) {
                fputcsv($output, [
                    $safeCsv($item->staff->staff_number),
                    $safeCsv($item->account_name_snapshot),
                    $safeCsv($item->bank_name_snapshot),
                    $item->bank_account_snapshot,
                    $item->net_salary,
                    $safeCsv('University payroll '.$cycle->payroll_month),
                ], ',', '"', '');
            }
            fclose($output);
        }, 'approved_payroll_'.$cycle->payroll_month.'.csv', ['Content-Type'=>'text/csv; charset=UTF-8']);
    }

    public function payslip(PayrollItem $item)
    {
        $role = auth()->user()->role;
        if ($role === 'employee') abort_unless(auth()->user()->staff_id === $item->staff_id,403);
        else abort_unless(in_array($role,['payroll','finance_approver','accounts','auditor'],true),403);
        $item->load(['staff.department','cycle']);
        abort_unless(in_array($item->cycle->status,['approved','payment_recorded'],true),403,'Payslips are available after approval.');
        return view('payroll.payslip',['item'=>$item]);
    }

    public function myPayslips()
    {
        abort_unless(auth()->user()->staff_id,403,'No linked staff record.');
        return view('payroll.my-payslips', ['items'=>PayrollItem::with('cycle')
            ->where('staff_id',auth()->user()->staff_id)
            ->whereHas('cycle',fn($q)=>$q->whereIn('status',['approved','payment_recorded']))
            ->latest()->paginate(15)]);
    }

    public function recordPayment(Request $request, PayrollCycle $cycle)
    {
        $data = $request->validate([
            'method'=>['required','in:bank,cheque'],
            'external_reference'=>['required','string','max:120'],
        ]);
        DB::transaction(function () use ($cycle,$data) {
            $locked = PayrollCycle::whereKey($cycle->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'approved',409,'Approval is required and payment may be recorded only once.');
            $total = $locked->items()->get()->sum(fn($item)=>Money::toKobo($item->net_salary));
            PaymentRecord::create([
                'source_type'=>'payroll','source_id'=>$locked->id,'amount'=>Money::fromKobo((int)$total),
                'method'=>$data['method'],'external_reference'=>$data['external_reference'],
                'recorded_by'=>auth()->id(),'recorded_at'=>now(),
            ]);
            $locked->update(['status'=>'payment_recorded']);
            Audit::record('payroll.payment_recorded','payroll',$locked->id);
        });
        return back()->with('success','External payment reference recorded (not independently bank-verified).');
    }
}
