<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PayrollCycle extends Model
{
    protected $fillable = ['payroll_month','status','prepared_by','approved_by','approved_at'];
    protected function casts(): array { return ['approved_at' => 'datetime']; }
    public function items() { return $this->hasMany(PayrollItem::class); }
    public function preparer() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function paymentRecord() { return $this->hasOne(PaymentRecord::class, 'source_id')->where('source_type', 'payroll'); }
}
