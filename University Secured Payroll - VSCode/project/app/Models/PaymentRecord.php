<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentRecord extends Model
{
    protected $fillable = ['source_type','source_id','amount','method','external_reference','recorded_by','recorded_at'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'recorded_at' => 'datetime']; }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}
