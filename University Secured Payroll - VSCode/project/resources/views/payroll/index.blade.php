@extends('layouts.app')
@section('title','Payroll cycles')
@section('content')
<div class="page-heading"><div><p class="eyebrow">SALARY PROCESSING</p><h1>Payroll cycles</h1><p class="muted">Prepare, review, approve and export salary batches.</p></div>@if(auth()->user()->role==='payroll')<a class="btn primary" href="{{ route('payroll.create') }}">+ New payroll</a>@endif</div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Period</th><th>Employees</th><th>Prepared by</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($cycles as $cycle)<tr><td><strong>{{ $cycle->payroll_month }}</strong></td><td>{{ $cycle->items_count }}</td><td>{{ $cycle->preparer->name }}</td><td><span class="pill">{{ str_replace('_',' ', $cycle->status) }}</span></td><td><a href="{{ route('payroll.show',$cycle) }}">Open →</a></td></tr>
@empty<tr><td colspan="5" class="muted">No payroll periods yet.</td></tr>@endforelse</tbody></table></div><div class="pager">{{ $cycles->links() }}</div></div>
@endsection
