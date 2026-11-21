<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Staff;
use App\Services\Audit;
use App\Services\Money;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        return view('staff.index', ['staff' => Staff::with('department')
            ->when($q, fn ($query) => $query->where(fn ($x) => $x->where('name', 'like', "%{$q}%")->orWhere('staff_number', 'like', "%{$q}%")))
            ->orderBy('name')->paginate(15)->withQueryString(), 'q' => $q]);
    }
    public function create() { return view('staff.form', ['staffMember' => new Staff(), 'departments' => Department::orderBy('name')->get()]); }
    public function edit(Staff $staff) { return view('staff.form', ['staffMember' => $staff, 'departments' => Department::orderBy('name')->get()]); }
    private function values(Request $request, ?Staff $staff = null): array
    {
        $fields = [
            'department_id' => ['required','exists:departments,id'],
            'staff_number' => ['required','string','max:35',Rule::unique('staff','staff_number')->ignore($staff?->id)],
            'name' => ['required','string','max:150'],
            'category' => ['required',Rule::in(['academic','non_academic'])],
            'grade' => ['required','string','max:50'],
            'bank_name' => ['required','string','max:120'],
            'bank_account_name' => ['required','string','max:150'],
            'basic_salary' => ['required','numeric','min:0','max:999999999'],
            'allowances' => ['required','numeric','min:0','max:999999999'],
            'deductions' => ['required','numeric','min:0','max:999999999'],
            'is_active' => ['required','boolean'],
        ];
        // The encrypted field is left unchanged on edit unless a replacement is supplied.
        $fields['bank_account_number'] = [$staff ? 'nullable' : 'required', 'regex:/^\d{10}$/'];
        $data = $request->validate($fields);
        try { Money::calculate((string)$data['basic_salary'], (string)$data['allowances'], (string)$data['deductions']); }
        catch (\InvalidArgumentException $e) { throw ValidationException::withMessages(['deductions' => $e->getMessage()]); }
        if (empty($data['bank_account_number'])) unset($data['bank_account_number']);
        return $data;
    }
    public function store(Request $request)
    {
        $staff = Staff::create($this->values($request));
        Audit::record('staff.created', 'staff', $staff->id);
        return redirect()->route('staff.index')->with('success','Staff record created.');
    }
    public function update(Request $request, Staff $staff)
    {
        $staff->update($this->values($request, $staff));
        Audit::record('staff.updated', 'staff', $staff->id);
        return redirect()->route('staff.index')->with('success','Staff record updated.');
    }
}
