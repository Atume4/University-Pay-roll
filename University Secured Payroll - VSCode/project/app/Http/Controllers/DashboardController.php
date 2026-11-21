<?php
namespace App\Http\Controllers;
use App\Models\FinancialRequest;
use App\Models\PayrollCycle;
use App\Models\Staff;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $requestCount = FinancialRequest::when(in_array($user->role, ['dept_officer', 'dept_approver'], true),
            fn ($q) => $q->where('department_id', $user->department_id))
            ->when($user->role === 'employee', fn ($q) => $q->whereRaw('1 = 0'))->count();
        return view('dashboard', [
            'staffCount' => Staff::where('is_active', true)->count(),
            'payrollCount' => PayrollCycle::count(),
            'requestCount' => $requestCount,
            'recentPayroll' => PayrollCycle::latest()->limit(5)->get(),
            'recentRequests' => in_array($user->role, ['dept_officer', 'dept_approver'], true)
                ? FinancialRequest::where('department_id', $user->department_id)->latest()->limit(5)->get()
                : (in_array($user->role, ['admin','finance_approver','accounts','auditor'], true)
                    ? FinancialRequest::latest()->limit(5)->get() : collect()),
        ]);
    }
}
