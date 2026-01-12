<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get ticket statistics
        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        // Get additional admin statistics
        $additionalStats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_agents' => User::where('role', 'agent')->count(),
            'high_priority_tickets' => Ticket::where('priority', 'high')->where('status', '!=', 'closed')->count(),
            'recent_tickets' => Ticket::orderBy('created_at', 'desc')->take(5)->get(),
        ];

        return view('dashboard', compact('ticketStats', 'additionalStats'));
    }

}