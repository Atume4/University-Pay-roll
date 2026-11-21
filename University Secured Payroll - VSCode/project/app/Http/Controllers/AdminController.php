<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public const ROLES = ['admin', 'hr', 'payroll', 'dept_officer', 'dept_approver', 'finance_approver', 'accounts', 'employee', 'auditor'];

    public function index()
    {
        return view('admin.users', [
            'users' => User::with('department')->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'staff' => Staff::orderBy('name')->get(),
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:12'],
            'role' => ['required',Rule::in(self::ROLES)],
            'department_id' => ['nullable','exists:departments,id'],
            'staff_id' => ['nullable','exists:staff,id'],
        ]);
        if (in_array($data['role'], ['dept_officer','dept_approver'], true) && empty($data['department_id'])) {
            return back()->withErrors(['department_id' => 'Department is required for departmental roles.'])->withInput();
        }
        if ($data['role'] === 'employee' && empty($data['staff_id'])) {
            return back()->withErrors(['staff_id' => 'Select a staff record for employee accounts.'])->withInput();
        }
        $user = User::create($data + ['is_active' => true]);
        Audit::record('user.created','user',$user->id, ['role'=>$user->role]);
        return redirect()->route('admin.users')->with('success','User created.');
    }

    public function toggle(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'You cannot disable your own account.');
        $user->update(['is_active' => ! $user->is_active]);
        Audit::record('user.active_changed','user',$user->id, ['active'=>$user->is_active]);
        return back()->with('success','Account status changed.');
    }
}
