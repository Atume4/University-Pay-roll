@extends('layouts.app')
@section('title','User administration')
@section('content')
<div class="page-heading"><div><p class="eyebrow">SECURITY ADMINISTRATION</p><h1>User accounts and roles</h1><p class="muted">Access permissions are enforced server-side, not only in the interface.</p></div></div>
<div class="panel"><h2>Existing accounts</h2><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ str_replace('_',' ',$user->role) }}</td><td>{{ $user->department?->code ?? '—' }}</td><td><span class="pill {{ $user->is_active?'green':'gray' }}">{{ $user->is_active?'Active':'Disabled' }}</span></td><td>@if($user->id!==auth()->id())<form method="post" action="{{ route('admin.users.toggle',$user) }}">@csrf<button class="btn small">{{ $user->is_active?'Disable':'Enable' }}</button></form>@endif</td></tr>@endforeach
</tbody></table></div></div>
<div class="panel form-panel"><h2>Add authorized account</h2><form action="{{ route('admin.users.store') }}" method="post">@csrf<div class="form-grid">
<label>Name<input name="name" required value="{{ old('name') }}"></label>
<label>Email<input type="email" name="email" required value="{{ old('email') }}"></label>
<label>Password (at least 12 characters)<input type="password" name="password" required minlength="12" autocomplete="new-password"></label>
<label>Role<select name="role" required><option value="">Choose role</option>@foreach($roles as $role)<option value="{{ $role }}" @selected(old('role')===$role)>{{ str_replace('_',' ',ucwords($role)) }}</option>@endforeach</select></label>
<label>Department (required for departmental roles)<select name="department_id"><option value="">Not assigned</option>@foreach($departments as $d)<option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>@endforeach</select></label>
<label>Staff link (required for employee)<select name="staff_id"><option value="">Not linked</option>@foreach($staff as $s)<option value="{{ $s->id }}" @selected(old('staff_id')==$s->id)>{{ $s->name }} ({{ $s->staff_number }})</option>@endforeach</select></label>
</div><button class="btn primary">Create account</button></form></div>
@endsection
