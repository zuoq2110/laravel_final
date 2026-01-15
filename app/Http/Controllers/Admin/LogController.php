<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with(['ticket', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(20);

        $tickets =Ticket::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name']);
        
        return view('admin.logs.index', compact('logs', 'tickets', 'users'));
    }
}