@extends('layouts.app')
@section('title','Financial request')
@section('content')
<div class="page-heading"><div><p class="eyebrow">{{ $item->reference }}</p><h1>{{ $item->title }}</h1><p class="muted">{{ $item->department->name }} · Requested by {{ $item->requester->name }}</p></div><a class="btn" href="{{ route('requests.index') }}">Back</a></div>
<div class="stat-grid smaller"><div class="stat"><div class="stat-label">Requested amount</div><div class="stat-value">₦{{ number_format((float)$item->amount,2) }}</div></div><div class="stat"><div class="stat-label">Current status</div><div class="stat-value small-value">{{ ucwords(str_replace('_',' ',$item->status)) }}</div></div></div>
<div class="panel"><h2>Purpose / justification</h2><p class="pre-wrap">{{ $item->purpose }}</p></div>
@if(auth()->user()->role==='dept_approver' && $item->status==='pending_department' && $item->department_id===auth()->user()->department_id)
<div class="panel"><h2>Departmental decision</h2><div class="two-cols"><form method="post" action="{{ route('requests.department-approve',$item) }}" class="form-stack">@csrf<label>Optional approval comment<textarea name="comment" rows="2"></textarea></label><button class="btn primary">Approve and forward to Finance</button></form><form method="post" action="{{ route('requests.reject',$item) }}" class="form-stack">@csrf<label>Rejection reason<textarea name="comment" required rows="2"></textarea></label><button class="btn danger">Reject request</button></form></div></div>
@endif
@if(auth()->user()->role==='finance_approver' && $item->status==='pending_finance')
<div class="panel"><h2>Final finance decision</h2><div class="two-cols"><form method="post" action="{{ route('requests.finance-approve',$item) }}" class="form-stack">@csrf<label>Optional approval comment<textarea name="comment" rows="2"></textarea></label><button class="btn primary">Authorize payment preparation</button></form><form method="post" action="{{ route('requests.reject',$item) }}" class="form-stack">@csrf<label>Rejection reason<textarea name="comment" required rows="2"></textarea></label><button class="btn danger">Reject request</button></form></div></div>
@endif
@if(in_array($item->status,['approved','payment_recorded']) && in_array(auth()->user()->role,['accounts','auditor']))<div class="actions"><a class="btn primary" href="{{ route('requests.voucher',$item) }}">View payment voucher / Print PDF</a></div>@endif
@if($item->paymentRecord)<div class="notice">Manually recorded payment: {{ $item->paymentRecord->method }} · {{ $item->paymentRecord->external_reference }} · recorded by {{ $item->paymentRecord->recorder->name }}. Not independently bank-verified.</div>@endif
@if($item->status==='approved' && auth()->user()->role==='accounts')<div class="panel form-panel"><h2>Record external payment reference</h2><p class="muted">Only after external payment has been independently confirmed.</p><form method="post" action="{{ route('requests.payment',$item) }}" class="form-grid">@csrf
<label>Method<select name="method"><option value="bank">Bank transfer</option><option value="cheque">Cheque</option></select></label>
<label>External reference<input maxlength="120" name="external_reference" required></label><div><button class="btn primary">Record payment</button></div></form></div>@endif
<div class="panel"><h2>Approval history</h2><div class="table-wrap"><table><thead><tr><th>Stage</th><th>Decision</th><th>Approver</th><th>Comment</th><th>Date</th></tr></thead><tbody>
@forelse($item->approvals as $approval)<tr><td>{{ ucfirst($approval->stage) }}</td><td><span class="pill">{{ $approval->decision }}</span></td><td>{{ $approval->user->name }}</td><td>{{ $approval->comment ?: '—' }}</td><td>{{ $approval->created_at->format('d M Y H:i') }}</td></tr>
@empty<tr><td colspan="5" class="muted">Awaiting first approval.</td></tr>@endforelse</tbody></table></div></div>
@endsection
