@extends('layouts.app')
@section('title','Employee payslip')
@section('content')
<div class="page-heading no-print"><div><h1>Employee payslip</h1><p class="muted">Print to PDF using your browser.</p></div><button class="btn primary" onclick="window.print()">Print / Save as PDF</button></div>
<div class="paper"><div class="document-heading"><div><p class="eyebrow">UNIVERSITY ADMINISTRATION</p><h1>SALARY PAYSLIP</h1></div><div class="muted">{{ $item->cycle->payroll_month }}</div></div>
<div class="document-grid"><p><strong>Staff:</strong> {{ $item->staff->name }}</p><p><strong>Staff ID:</strong> {{ $item->staff->staff_number }}</p><p><strong>Department:</strong> {{ $item->staff->department->name }}</p><p><strong>Salary grade:</strong> {{ $item->staff->grade }}</p></div>
<table class="document-table"><tr><th>Salary component</th><th>Amount (NGN)</th></tr><tr><td>Basic salary</td><td>{{ number_format((float)$item->basic_salary,2) }}</td></tr><tr><td>Allowances</td><td>{{ number_format((float)$item->allowances,2) }}</td></tr><tr><td>Gross salary</td><td>{{ number_format((float)$item->gross_salary,2) }}</td></tr><tr><td>Deductions</td><td>({{ number_format((float)$item->deductions,2) }})</td></tr><tr class="total-row"><td><strong>NET PAY</strong></td><td><strong>₦{{ number_format((float)$item->net_salary,2) }}</strong></td></tr></table>
<p class="doc-note">Academic research prototype — generated using simulated information. This is not a bank payment confirmation.</p></div>
@endsection
