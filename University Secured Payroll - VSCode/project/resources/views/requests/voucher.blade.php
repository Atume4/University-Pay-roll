@extends('layouts.app')
@section('title','Payment voucher')
@section('content')
<div class="page-heading no-print"><div><h1>Payment voucher</h1><p class="muted">Print or save as PDF. No bank instruction is automatically sent.</p></div><button class="btn primary" onclick="window.print()">Print / Save as PDF</button></div>
<div class="paper"><div class="document-heading"><div><p class="eyebrow">UNIVERSITY ADMINISTRATION</p><h1>APPROVED PAYMENT VOUCHER</h1></div><span>{{ $item->reference }}</span></div>
<div class="document-grid"><p><strong>Department:</strong> {{ $item->department->name }}</p><p><strong>Requested by:</strong> {{ $item->requester->name }}</p><p><strong>Purpose:</strong> {{ $item->title }}</p><p><strong>Status:</strong> {{ str_replace('_',' ',$item->status) }}</p></div>
<p class="pre-wrap">{{ $item->purpose }}</p><table class="document-table"><tr><td><strong>Approved amount</strong></td><td><strong>₦{{ number_format((float)$item->amount,2) }}</strong></td></tr>
@foreach($item->approvals as $approval)<tr><td>{{ ucfirst($approval->stage) }} authorization</td><td>{{ $approval->user->name }} ({{ $approval->created_at->format('d M Y') }})</td></tr>@endforeach
@if($item->paymentRecord)<tr><td>External payment reference</td><td>{{ $item->paymentRecord->external_reference }}</td></tr>@endif</table>
<p class="doc-note">Research prototype document. Requires independent institutional and banking verification before any real payment.</p></div>
@endsection
