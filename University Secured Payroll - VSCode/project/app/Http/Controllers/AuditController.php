<?php
namespace App\Http\Controllers;
use App\Models\AuditLog;
use Illuminate\Routing\Controller;
class AuditController extends Controller
{
    public function index() { return view('audit.index', ['logs' => AuditLog::with('user')->orderByDesc('id')->paginate(30)]); }
}
