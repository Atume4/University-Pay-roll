@extends('layouts.app')
@section('title','My payslips')
@section('content')
<div class="page-heading"><div><p class="eyebrow">EMPLOYEE SELF-SERVICE</p><h1>My payslips</h1><p class="muted">Approved salary records linked to your staff account.</p></div></div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Month</th><th>Net salary</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->cycle->payroll_month }}</td><td>₦{{ number_format((float)$item->net_salary,2) }}</td><td><a href="{{ route('payslips.show',$item) }}">Open payslip</a></td></tr>
@empty<tr><td colspan="3" class="muted">No approved payslips available.</td></tr>@endforelse
</tbody></table></div><div class="pager">{{ $items->links() }}</div></div>
@endsection
