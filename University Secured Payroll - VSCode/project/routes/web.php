<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class,'show'])->name('login');
    Route::post('/login', [AuthController::class,'login'])->middleware('throttle:5,1')->name('login.attempt');
});
Route::middleware(['auth','active'])->group(function () {
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::middleware('role:admin,hr,payroll,finance_approver,accounts,auditor')->group(function () {
        Route::get('/staff',[StaffController::class,'index'])->name('staff.index');
    });
    Route::middleware('role:admin,hr')->group(function () {
        Route::get('/staff/create',[StaffController::class,'create'])->name('staff.create');
        Route::post('/staff',[StaffController::class,'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit',[StaffController::class,'edit'])->name('staff.edit');
        Route::put('/staff/{staff}',[StaffController::class,'update'])->name('staff.update');
    });

    Route::middleware('role:admin,payroll,finance_approver,accounts,auditor')->group(function () {
        Route::get('/payroll',[PayrollController::class,'index'])->name('payroll.index');
        Route::get('/payroll/{cycle}',[PayrollController::class,'show'])->name('payroll.show');
    });
    Route::middleware('role:payroll')->group(function () {
        Route::get('/payroll-new',[PayrollController::class,'create'])->name('payroll.create');
        Route::post('/payroll-new',[PayrollController::class,'store'])->name('payroll.store');
        Route::post('/payroll/{cycle}/submit',[PayrollController::class,'submit'])->name('payroll.submit');
    });
    Route::post('/payroll/{cycle}/approve',[PayrollController::class,'approve'])->middleware('role:finance_approver')->name('payroll.approve');
    Route::get('/payroll/{cycle}/bank-export',[PayrollController::class,'export'])->middleware('role:accounts')->name('payroll.export');
    Route::post('/payroll/{cycle}/payment-record',[PayrollController::class,'recordPayment'])->middleware('role:accounts')->name('payroll.payment');
    Route::get('/my-payslips',[PayrollController::class,'myPayslips'])->middleware('role:employee')->name('payslips.mine');
    Route::get('/payslips/{item}',[PayrollController::class,'payslip'])->name('payslips.show');

    Route::middleware('role:admin,dept_officer,dept_approver,finance_approver,accounts,auditor')->group(function () {
        Route::get('/requests',[FinancialRequestController::class,'index'])->name('requests.index');
        Route::get('/requests/{item}',[FinancialRequestController::class,'show'])->name('requests.show');
    });
    Route::middleware('role:dept_officer')->group(function () {
        Route::get('/requests-new',[FinancialRequestController::class,'create'])->name('requests.create');
        Route::post('/requests-new',[FinancialRequestController::class,'store'])->name('requests.store');
    });
    Route::post('/requests/{item}/department-approve',[FinancialRequestController::class,'approveDepartment'])
        ->middleware('role:dept_approver')->name('requests.department-approve');
    Route::post('/requests/{item}/finance-approve',[FinancialRequestController::class,'approveFinance'])
        ->middleware('role:finance_approver')->name('requests.finance-approve');
    Route::post('/requests/{item}/reject',[FinancialRequestController::class,'reject'])
        ->middleware('role:dept_approver,finance_approver')->name('requests.reject');
    Route::get('/requests/{item}/voucher',[FinancialRequestController::class,'voucher'])
        ->middleware('role:accounts,auditor')->name('requests.voucher');
    Route::post('/requests/{item}/payment-record',[FinancialRequestController::class,'recordPayment'])
        ->middleware('role:accounts')->name('requests.payment');

    Route::get('/audit',[AuditController::class,'index'])->middleware('role:admin,auditor')->name('audit.index');
    Route::get('/users',[AdminController::class,'index'])->middleware('role:admin')->name('admin.users');
    Route::post('/users',[AdminController::class,'store'])->middleware('role:admin')->name('admin.users.store');
    Route::post('/users/{user}/toggle',[AdminController::class,'toggle'])->middleware('role:admin')->name('admin.users.toggle');
});
