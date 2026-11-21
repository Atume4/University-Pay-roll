<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PayrollItem extends Model
{
    protected $fillable = ['payroll_cycle_id','staff_id','basic_salary','allowances','deductions','gross_salary','net_salary','bank_account_snapshot','bank_name_snapshot','account_name_snapshot'];
    protected function casts(): array
    {
        return ['bank_account_snapshot' => 'encrypted', 'basic_salary' => 'decimal:2','allowances' => 'decimal:2','deductions' => 'decimal:2','gross_salary' => 'decimal:2','net_salary' => 'decimal:2'];
    }
    public function staff() { return $this->belongsTo(Staff::class); }
    public function cycle() { return $this->belongsTo(PayrollCycle::class, 'payroll_cycle_id'); }
}
