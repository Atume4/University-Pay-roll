<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'University Payroll') — Payroll & Payments</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@if(auth()->check())
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}"><span class="brand-icon">U</span><span>UNIVERSITY<br><small>PAYROLL SYSTEM</small></span></a>
        <div class="section-label">WORKSPACE</div>
        <nav>
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">◫ &nbsp; Overview</a>
            @if(in_array(auth()->user()->role,['admin','hr','payroll','finance_approver','accounts','auditor']))
            <a class="{{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">◉ &nbsp; Staff directory</a>
            @endif
            @if(in_array(auth()->user()->role,['admin','payroll','finance_approver','accounts','auditor']))
            <a class="{{ request()->routeIs('payroll.*') ? 'active' : '' }}" href="{{ route('payroll.index') }}">▤ &nbsp; Payroll cycles</a>
            @endif
            @if(in_array(auth()->user()->role,['admin','dept_officer','dept_approver','finance_approver','accounts','auditor']))
            <a class="{{ request()->routeIs('requests.*') ? 'active' : '' }}" href="{{ route('requests.index') }}">⇄ &nbsp; Payment requests</a>
            @endif
            @if(auth()->user()->role==='employee')<a href="{{ route('payslips.mine') }}">▤ &nbsp; My payslips</a>@endif
            @if(in_array(auth()->user()->role,['admin','auditor']))<a href="{{ route('audit.index') }}">◷ &nbsp; Audit log</a>@endif
            @if(auth()->user()->role==='admin')<a href="{{ route('admin.users') }}">♙ &nbsp; User administration</a>@endif
        </nav>
        <div class="sidebar-bottom">
            <div class="env-tag">● LOCALHOST DEMO</div>
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">{{ str_replace('_', ' ', strtoupper(auth()->user()->role)) }}</div>
            <form action="{{ route('logout') }}" method="post">@csrf<button type="submit" class="logout-btn">Sign out</button></form>
        </div>
    </aside>
    <div class="main-wrap">
        <header class="topbar"><div class="crumb">University Administration <span>/</span> @yield('title','Overview')</div><span class="topbar-note">Research prototype · simulated records</span></header>
        <main class="main-content">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error"><strong>Please correct the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
        <footer class="footer">Secured University Payroll Management System · Academic prototype · No live bank integration</footer>
    </div>
</div>
@else
    @yield('content')
@endif
</body>
</html>
