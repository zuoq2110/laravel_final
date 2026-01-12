<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Log::with(['ticket', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by ticket ID if provided
        if ($request->filled('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        // Filter by user ID if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action if provided
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(20);

        // Get unique ticket IDs and users for filter dropdowns
        $tickets =Ticket::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name']);
        
        return view('admin.logs.index', compact('logs', 'tickets', 'users'));
    }
}