<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Approval extends Model
{
    protected $fillable = ['financial_request_id','user_id','stage','decision','comment'];
    public function user() { return $this->belongsTo(User::class); }
}
