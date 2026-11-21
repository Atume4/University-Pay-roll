<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FinancialRequest extends Model
{
    protected $fillable = ['reference','department_id','requested_by','title','purpose','amount','status'];
    protected function casts(): array { return ['amount' => 'decimal:2']; }
    public function department() { return $this->belongsTo(Department::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approvals() { return $this->hasMany(Approval::class); }
    public function paymentRecord() { return $this->hasOne(PaymentRecord::class, 'source_id')->where('source_type', 'request'); }
}
