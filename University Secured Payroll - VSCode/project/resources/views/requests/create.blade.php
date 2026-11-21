@extends('layouts.app')
@section('title','New financial request')
@section('content')
<div class="page-heading"><div><p class="eyebrow">DEPARTMENTAL FINANCE</p><h1>Submit payment request</h1><p class="muted">Your request is assigned to your department and sent for departmental approval.</p></div><a class="btn" href="{{ route('requests.index') }}">Back</a></div>
<div class="panel form-panel"><form action="{{ route('requests.store') }}" method="post" class="form-stack">@csrf
<label>Request title<input maxlength="150" required name="title" value="{{ old('title') }}" placeholder="e.g. Laboratory maintenance supplies"></label>
<label>Purpose and justification<textarea name="purpose" required rows="5">{{ old('purpose') }}</textarea></label>
<label>Amount (₦)<input type="number" step="0.01" min="0.01" max="999999999" name="amount" required value="{{ old('amount') }}"></label>
<button class="btn primary">Send for departmental approval</button></form></div>
@endsection
