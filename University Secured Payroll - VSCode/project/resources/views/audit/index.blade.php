@extends('layouts.app')
@section('title','Audit log')
@section('content')
<div class="page-heading"><div><p class="eyebrow">AUDIT & ACCOUNTABILITY</p><h1>Recorded system activities</h1><p class="muted">Sensitive bank information and passwords are never included in audit metadata.</p></div></div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Time</th><th>User</th><th>Action</th><th>Record</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td>{{ $log->created_at?->format('d M Y H:i:s') }}</td><td>{{ $log->user?->name ?? 'System' }}</td><td><strong>{{ $log->action }}</strong></td><td>{{ $log->entity_type }} #{{ $log->entity_id }}</td></tr>
@empty<tr><td colspan="4" class="muted">No events recorded.</td></tr>@endforelse</tbody></table></div><div class="pager">{{ $logs->links() }}</div></div>
@endsection
