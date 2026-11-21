@extends('layouts.app')
@section('title','Staff directory')
@section('content')
<div class="page-heading"><div><p class="eyebrow">HUMAN RESOURCES</p><h1>Staff directory</h1><p class="muted">Bank account numbers are masked in this view.</p></div>@if(in_array(auth()->user()->role,['admin','hr']))<a class="btn primary" href="{{ route('staff.create') }}">+ Add staff</a>@endif</div>
<div class="panel">
<form method="get" class="filter-row"><input type="search" name="q" value="{{ $q }}" placeholder="Search name or staff number"><button class="btn" type="submit">Search</button></form>
<div class="table-wrap"><table><thead><tr><th>Staff no.</th><th>Name</th><th>Department</th><th>Grade</th><th>Account</th><th>Basic salary</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($staff as $item)
<tr><td>{{ $item->staff_number }}</td><td><strong>{{ $item->name }}</strong></td><td>{{ $item->department->code }}</td><td>{{ $item->grade }}</td><td>{{ $item->masked_account }}</td><td>₦{{ number_format((float)$item->basic_salary,2) }}</td><td><span class="pill {{ $item->is_active?'green':'gray' }}">{{ $item->is_active?'Active':'Inactive' }}</span></td><td>@if(in_array(auth()->user()->role,['admin','hr']))<a href="{{ route('staff.edit',$item) }}">Edit</a>@endif</td></tr>
@empty<tr><td colspan="8" class="muted">No staff records found.</td></tr>@endforelse
</tbody></table></div>
<div class="pager">{{ $staff->links() }}</div>
</div>
@endsection
