@extends('layouts.app')
@section('title','Overview')
@section('content')
<div class="page-heading"><div><p class="eyebrow">OPERATIONS CENTER</p><h1>University finance overview</h1><p class="muted">Payroll preparation, payment requests and approval monitoring in one secure workspace.</p></div></div>
<div class="stat-grid">
    <div class="stat"><div class="stat-icon">◉</div><div class="stat-label">Active staff</div><div class="stat-value">{{ $staffCount }}</div><div class="stat-foot">Registered records</div></div>
    <div class="stat"><div class="stat-icon">▤</div><div class="stat-label">Payroll cycles</div><div class="stat-value">{{ $payrollCount }}</div><div class="stat-foot">All periods</div></div>
    <div class="stat"><div class="stat-icon">⇄</div><div class="stat-label">Financial requests</div><div class="stat-value">{{ $requestCount }}</div><div class="stat-foot">Accessible to your role</div></div>
</div>
<div class="notice"><strong>Prototype safeguard:</strong> Money transfers do not occur here. Approved payment schedules are exported for external banking, and recorded payment references are manually supplied by Accounts.</div>
@if(auth()->user()->role==='employee')
    <div class="panel"><h2>Your salary records</h2><p>Use My Payslips to view payroll periods approved for your staff account.</p><a class="btn primary" href="{{ route('payslips.mine') }}">View my payslips</a></div>
@else
<div class="two-cols">
    <div class="panel"><div class="panel-head"><h2>Latest payroll cycles</h2>@if(in_array(auth()->user()->role,['admin','payroll','finance_approver','accounts','auditor']))<a href="{{ route('payroll.index') }}">View all</a>@endif</div>
        <div class="table-wrap"><table><thead><tr><th>Period</th><th>Status</th></tr></thead><tbody>
        @forelse($recentPayroll as $cycle)<tr><td>{{ $cycle->payroll_month }}</td><td><span class="pill">{{ str_replace('_',' ',ucwords($cycle->status)) }}</span></td></tr>
        @empty<tr><td colspan="2" class="muted">No payroll cycles yet.</td></tr>@endforelse
        </tbody></table></div>
    </div>
    <div class="panel"><div class="panel-head"><h2>Recent departmental requests</h2>@if(in_array(auth()->user()->role,['admin','dept_officer','dept_approver','finance_approver','accounts','auditor']))<a href="{{ route('requests.index') }}">View all</a>@endif</div>
        <div class="table-wrap"><table><thead><tr><th>Request</th><th>Status</th></tr></thead><tbody>
        @forelse($recentRequests as $item)<tr><td>{{ $item->reference }}</td><td><span class="pill">{{ str_replace('_',' ', $item->status) }}</span></td></tr>
        @empty<tr><td colspan="2" class="muted">No requests to display.</td></tr>@endforelse
        </tbody></table></div>
    </div>
</div>
@endif
@endsection
