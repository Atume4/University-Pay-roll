@extends('layouts.app')
@section('title','Payment requests')
@section('content')
<div class="page-heading"><div><p class="eyebrow">DEPARTMENTAL FINANCE</p><h1>Payment requests</h1><p class="muted">Department approval → Finance approval → Accounts payment record.</p></div>@if(auth()->user()->role==='dept_officer')<a class="btn primary" href="{{ route('requests.create') }}">+ Submit request</a>@endif</div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Department</th><th>Purpose</th><th>Amount</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($requests as $item)<tr><td><strong>{{ $item->reference }}</strong></td><td>{{ $item->department->code }}</td><td>{{ $item->title }}</td><td>₦{{ number_format((float)$item->amount,2) }}</td><td><span class="pill">{{ str_replace('_',' ', $item->status) }}</span></td><td><a href="{{ route('requests.show',$item) }}">Open →</a></td></tr>
@empty<tr><td colspan="6" class="muted">No financial requests yet.</td></tr>@endforelse</tbody></table></div><div class="pager">{{ $requests->links() }}</div></div>
@endsection
