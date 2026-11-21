@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="login-page"><div class="login-card">
    <div class="login-icon">U</div>
    <p class="eyebrow">UNIVERSITY ADMINISTRATION</p>
    <h1>Secure payroll.<br>Clear approvals.</h1>
    <p class="muted">Sign in to the local research prototype.</p>
    @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
    <form action="{{ route('login.attempt') }}" method="post" class="form-stack">@csrf
        <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required placeholder="payroll@demo.test"></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
        <button class="btn primary full" type="submit">Sign in securely</button>
    </form>
    <div class="demo-note"><strong>Demo accounts</strong> are provided in the accompanying README. All records and bank numbers are fictional.</div>
</div></div>
@endsection
