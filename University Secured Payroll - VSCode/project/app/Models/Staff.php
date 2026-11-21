<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Staff extends Model
{
    protected $table = 'staff';
    protected $fillable = ['department_id','staff_number','name','category','grade','bank_name','bank_account_number','bank_account_name','basic_salary','allowances','deductions','is_active'];
    protected function casts(): array
    {
        return ['bank_account_number' => 'encrypted', 'is_active' => 'boolean', 'basic_salary' => 'decimal:2', 'allowances' => 'decimal:2', 'deductions' => 'decimal:2'];
    }
    public function department() { return $this->belongsTo(Department::class); }
    public function payrollItems() { return $this->hasMany(PayrollItem::class); }
    public function getMaskedAccountAttribute(): string
    {
        $number = (string) $this->bank_account_number;
        return str_repeat('•', max(0, strlen($number) - 4)).substr($number, -4);
    }
}
