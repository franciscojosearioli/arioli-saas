<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('audit_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $auditLogs = AuditLog::all();

        return view('admin.auditLogs.index', compact('auditLogs'));
    }

    public function show(AuditLog $auditLog)
    {
        abort_if(Gate::denies('audit_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.auditLogs.show', compact('auditLog'));
    }

    public function markAllAsRead()
    {
    
        $user = auth()->user();
        
        $user->auditLogs()->where('read', false)->update(['read' => true]);

        $logs = $user->auditLogs()->limit(10)->orderBy('created_at', 'ASC')->get()->reverse();


        $logsCount = $user->auditLogs()->where('read', false)->count();

        $html = view('partials.logs', compact('logs'))->render();
    
        return response()->json(['html' => $html, 'logsCount' => $logsCount]);
    }




    public function latestLogs()
    {
        $latestLogs = AuditLog::latest()->take(5)->get();

        return $latestLogs;

    }

}