@extends('layouts.app')
@section('title','Prepare payroll')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MONTHLY PAYROLL</p><h1>Prepare salary schedule</h1><p class="muted">All active staff are included. Values and bank details are snapshotted for this cycle.</p></div><a class="btn" href="{{ route('payroll.index') }}">Back</a></div>
<div class="panel form-panel"><div class="notice"><strong>{{ $activeCount }} active staff</strong> will be included. Only the Finance Approver can authorize payment export.</div>
<form action="{{ route('payroll.store') }}" method="post" class="form-stack">@csrf
<label>Payroll month<input type="month" required name="payroll_month" value="{{ old('payroll_month',now()->format('Y-m')) }}"></label>
<button class="btn primary" type="submit">Generate draft payroll</button>
</form></div>
@endsection
