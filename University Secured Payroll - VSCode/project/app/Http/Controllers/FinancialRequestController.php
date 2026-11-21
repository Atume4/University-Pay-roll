<?php
namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\FinancialRequest;
use App\Models\PaymentRecord;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinancialRequestController extends Controller
{
    private function canView(FinancialRequest $item): void
    {
        $user = auth()->user();
        if (in_array($user->role,['dept_officer','dept_approver'],true)) {
            abort_unless($user->department_id === $item->department_id,403);
        } else abort_unless(in_array($user->role,['admin','finance_approver','accounts','auditor'],true),403);
    }
    public function index()
    {
        $user=auth()->user();
        return view('requests.index', ['requests'=>FinancialRequest::with(['department','requester'])
            ->when(in_array($user->role,['dept_officer','dept_approver'],true),fn($q)=>$q->where('department_id',$user->department_id))
            ->latest()->paginate(20)]);
    }
    public function create() { return view('requests.create'); }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->department_id,403,'A department must be assigned.');
        $data = $request->validate([
            'title'=>['required','string','max:150'],
            'purpose'=>['required','string','max:2000'],
            'amount'=>['required','numeric','decimal:0,2','gt:0','max:999999999'],
        ]);
        $item = FinancialRequest::create($data + [
            'reference'=>'REQ-'.Str::upper(Str::random(12)),
            'requested_by'=>auth()->id(), 'department_id'=>auth()->user()->department_id,
            'status'=>'pending_department',
        ]);
        Audit::record('request.created','request',$item->id);
        return redirect()->route('requests.show',$item)->with('success','Request submitted for departmental approval.');
    }
    public function show(FinancialRequest $item)
    {
        $this->canView($item);
        $item->load(['department','requester','approvals.user','paymentRecord.recorder']);
        return view('requests.show',['item'=>$item]);
    }
    public function approveDepartment(Request $request, FinancialRequest $item)
    {
        $data = $request->validate(['comment'=>['nullable','string','max:1000']]);
        DB::transaction(function () use ($item,$data) {
            $locked=FinancialRequest::whereKey($item->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status==='pending_department',409,'Wrong approval stage.');
            abort_unless($locked->department_id===auth()->user()->department_id,403,'Different department.');
            abort_if($locked->requested_by===auth()->id(),403,'Requester may not approve their own request.');
            Approval::create(['financial_request_id'=>$locked->id,'user_id'=>auth()->id(),
                'stage'=>'department','decision'=>'approved','comment'=>$data['comment']??null]);
            $locked->update(['status'=>'pending_finance']);
            Audit::record('request.department_approved','request',$locked->id);
        });
        return back()->with('success','Forwarded to finance approval.');
    }
    public function approveFinance(Request $request, FinancialRequest $item)
    {
        $data=$request->validate(['comment'=>['nullable','string','max:1000']]);
        DB::transaction(function () use ($item,$data) {
            $locked=FinancialRequest::whereKey($item->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status==='pending_finance',409,'Department approval required first.');
            abort_if($locked->requested_by===auth()->id(),403,'Requester may not approve their own request.');
            Approval::create(['financial_request_id'=>$locked->id,'user_id'=>auth()->id(),
                'stage'=>'finance','decision'=>'approved','comment'=>$data['comment']??null]);
            $locked->update(['status'=>'approved']);
            Audit::record('request.finance_approved','request',$locked->id);
        });
        return back()->with('success','Request approved for payment preparation.');
    }
    public function reject(Request $request, FinancialRequest $item)
    {
        $data=$request->validate(['comment'=>['required','string','max:1000']]);
        DB::transaction(function () use ($item,$data) {
            $locked=FinancialRequest::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $role=auth()->user()->role;
            $stage=$locked->status==='pending_department' ? 'department' : ($locked->status==='pending_finance'?'finance':null);
            abort_unless($stage,409,'This request is not awaiting approval.');
            if ($stage==='department') abort_unless($role==='dept_approver' && $locked->department_id===auth()->user()->department_id,403);
            else abort_unless($role==='finance_approver',403);
            abort_if($locked->requested_by===auth()->id(),403);
            Approval::create(['financial_request_id'=>$locked->id,'user_id'=>auth()->id(),
                'stage'=>$stage,'decision'=>'rejected','comment'=>$data['comment']]);
            $locked->update(['status'=>'rejected']);
            Audit::record('request.rejected','request',$locked->id,['stage'=>$stage]);
        });
        return back()->with('success','Request rejected and recorded.');
    }
    public function voucher(FinancialRequest $item)
    {
        abort_unless(in_array($item->status,['approved','payment_recorded'],true),403,'Finance approval required.');
        $item->load(['department','requester','approvals.user','paymentRecord']);
        Audit::record('request.voucher_viewed','request',$item->id);
        return view('requests.voucher',['item'=>$item]);
    }
    public function recordPayment(Request $request, FinancialRequest $item)
    {
        $data=$request->validate(['method'=>['required','in:bank,cheque'],'external_reference'=>['required','string','max:120']]);
        DB::transaction(function () use ($item,$data) {
            $locked=FinancialRequest::whereKey($item->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status==='approved',409,'Approved requests may be recorded once only.');
            PaymentRecord::create(['source_type'=>'request','source_id'=>$locked->id,
                'amount'=>$locked->amount,'method'=>$data['method'],
                'external_reference'=>$data['external_reference'],
                'recorded_by'=>auth()->id(),'recorded_at'=>now()]);
            $locked->update(['status'=>'payment_recorded']);
            Audit::record('request.payment_recorded','request',$locked->id);
        });
        return back()->with('success','External payment reference recorded (not bank-verified).');
    }
}
