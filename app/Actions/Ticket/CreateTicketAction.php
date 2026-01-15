<?php

namespace App\Actions\Ticket;

use App\Mail\NewTicketNotification;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\LogRepositoryInterface;
use App\Repositories\TicketRepository;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private LogRepositoryInterface $logRepository
    ){}

    public function execute(array $data) : Ticket {
        $ticketData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'user_id' => Auth::id(),
            'status' => 'open'
        ];

        $ticket = $this->ticketRepository->create($ticketData);

        if(!empty($data['categories'])){
            $ticket->categories()->attach($data['categories']);
        }
        $this->logRepository->create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'action'    => 'create',
            'description' => "Ticket created successfully!",
        ]);

        $this->sendAdminNotifications($ticket);

        return $ticket;
    }

    private function sendAdminNotifications(Ticket $ticket): void
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        Log::info('Sending email notifications for new ticket', [
            'ticket_id' => $ticket->id,
            'admin_count' => $adminUsers->count()
        ]);
        
        foreach ($adminUsers as $admin) {
            Log::info('Sending email to admin', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email
            ]);
            
            Mail::to($admin->email)->send(new NewTicketNotification($ticket));
        }
        
        Log::info('Finished sending email notifications');
    }
}