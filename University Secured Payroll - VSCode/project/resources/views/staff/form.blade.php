@extends('layouts.app')
@section('title',$staffMember->exists?'Edit staff':'Register staff')
@section('content')
<div class="page-heading"><div><p class="eyebrow">HUMAN RESOURCES</p><h1>{{ $staffMember->exists?'Edit staff record':'Register staff member' }}</h1><p class="muted">Account details are encrypted at rest and masked in directory views.</p></div><a class="btn" href="{{ route('staff.index') }}">Back</a></div>
<div class="panel form-panel"><form method="post" action="{{ $staffMember->exists ? route('staff.update',$staffMember) : route('staff.store') }}">@csrf @if($staffMember->exists)@method('PUT')@endif
<h2>Staff profile</h2><div class="form-grid">
<label>Staff number<input required maxlength="35" name="staff_number" value="{{ old('staff_number',$staffMember->staff_number) }}"></label>
<label>Full name<input required name="name" value="{{ old('name',$staffMember->name) }}"></label>
<label>Department<select required name="department_id"><option value="">Select department</option>@foreach($departments as $dept)<option value="{{ $dept->id }}" @selected(old('department_id',$staffMember->department_id)==$dept->id)>{{ $dept->name }}</option>@endforeach</select></label>
<label>Staff category<select name="category"><option value="academic" @selected(old('category',$staffMember->category)==='academic')>Academic</option><option value="non_academic" @selected(old('category',$staffMember->category)==='non_academic')>Non-academic</option></select></label>
<label>Salary grade<input required name="grade" value="{{ old('grade',$staffMember->grade) }}"></label>
<label>Employment status<select name="is_active"><option value="1" @selected(old('is_active',$staffMember->exists?($staffMember->is_active?1:0):1)==1)>Active</option><option value="0" @selected(old('is_active',$staffMember->exists?($staffMember->is_active?1:0):1)==0)>Inactive</option></select></label>
</div><h2>Bank details</h2><div class="form-grid">
<label>Bank name<input required name="bank_name" value="{{ old('bank_name',$staffMember->bank_name) }}"></label>
<label>Bank account name<input required name="bank_account_name" value="{{ old('bank_account_name',$staffMember->bank_account_name) }}"></label>
<label>10-digit account number<input name="bank_account_number" pattern="[0-9]{10}" inputmode="numeric" autocomplete="off" {{ $staffMember->exists?'':'required' }} placeholder="{{ $staffMember->exists?'Leave blank to keep existing number':'10 digits' }}"></label>
</div><h2>Salary figures (NGN)</h2><div class="form-grid">
<label>Basic salary<input required type="number" min="0" step="0.01" name="basic_salary" value="{{ old('basic_salary',$staffMember->basic_salary ?? '0.00') }}"></label>
<label>Allowances<input required type="number" min="0" step="0.01" name="allowances" value="{{ old('allowances',$staffMember->allowances ?? '0.00') }}"></label>
<label>Deductions<input required type="number" min="0" step="0.01" name="deductions" value="{{ old('deductions',$staffMember->deductions ?? '0.00') }}"></label>
</div><button class="btn primary" type="submit">Save staff record</button></form></div>
@endsection
