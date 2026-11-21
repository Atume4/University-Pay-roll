@extends('layouts.app')
@section('title','Payroll details')
@section('content')
<div class="page-heading"><div><p class="eyebrow">PAYROLL {{ $cycle->payroll_month }}</p><h1>Salary schedule</h1><p class="muted">Prepared by {{ $cycle->preparer->name }} · <span class="pill">{{ str_replace('_',' ', $cycle->status) }}</span></p></div><a class="btn" href="{{ route('payroll.index') }}">Back</a></div>
<div class="stat-grid smaller"><div class="stat"><div class="stat-label">Staff included</div><div class="stat-value">{{ $cycle->items->count() }}</div></div><div class="stat"><div class="stat-label">Total net pay</div><div class="stat-value">₦{{ number_format((float)$total,2) }}</div></div><div class="stat"><div class="stat-label">Approval state</div><div class="stat-value small-value">{{ ucwords(str_replace('_',' ',$cycle->status)) }}</div></div></div>
<div class="actions">
@if($cycle->status==='draft' && auth()->user()->role==='payroll' && $cycle->prepared_by===auth()->id())<form method="post" action="{{ route('payroll.submit',$cycle) }}">@csrf<button class="btn primary">Submit for finance approval</button></form>@endif
@if($cycle->status==='submitted' && auth()->user()->role==='finance_approver')<form method="post" action="{{ route('payroll.approve',$cycle) }}">@csrf<button class="btn primary">Authorize payroll</button></form>@endif
@if(in_array($cycle->status,['approved','payment_recorded']) && auth()->user()->role==='accounts')<a class="btn primary" href="{{ route('payroll.export',$cycle) }}">Download bank schedule CSV</a>@endif
</div>
@if($cycle->paymentRecord)<div class="notice">Recorded external payment: {{ $cycle->paymentRecord->method }} · Reference: {{ $cycle->paymentRecord->external_reference }}. This is a manually entered record, not bank verification.</div>@endif
@if($cycle->status==='approved' && auth()->user()->role==='accounts')
<div class="panel form-panel"><h2>Record external payment reference</h2><p class="muted">Complete this only after an external bank or cheque payment has been separately confirmed.</p><form class="form-grid" method="post" action="{{ route('payroll.payment',$cycle) }}">@csrf
<label>Payment method<select name="method"><option value="bank">Bank transfer</option><option value="cheque">Cheque</option></select></label>
<label>External reference<input required name="external_reference" maxlength="120"></label><div><button class="btn primary">Record payment</button></div></form></div>
@endif
<div class="panel"><div class="panel-head"><h2>Payroll items</h2><span class="muted">Snapshot values preserved from creation</span></div><div class="table-wrap"><table><thead><tr><th>Staff</th><th>Basic</th><th>Allowances</th><th>Deductions</th><th>Gross</th><th>Net salary</th><th>Payslip</th></tr></thead><tbody>
@foreach($cycle->items as $item)<tr><td><strong>{{ $item->staff->name }}</strong><div class="tiny">{{ $item->staff->staff_number }}</div></td><td>₦{{ number_format((float)$item->basic_salary,2) }}</td><td>₦{{ number_format((float)$item->allowances,2) }}</td><td>₦{{ number_format((float)$item->deductions,2) }}</td><td>₦{{ number_format((float)$item->gross_salary,2) }}</td><td><strong>₦{{ number_format((float)$item->net_salary,2) }}</strong></td><td>@if(in_array($cycle->status,['approved','payment_recorded']) && in_array(auth()->user()->role,['payroll','finance_approver','accounts','auditor']))<a href="{{ route('payslips.show',$item) }}">View</a>@else—@endif</td></tr>@endforeach
</tbody></table></div></div>
@endsection
