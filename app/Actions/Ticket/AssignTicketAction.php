<?php

namespace App\Actions\Ticket;

use App\Events\TicketAssignedEvent;
use App\Models\Log;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\LogRepositoryInterface;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use League\Config\Exception\ValidationException;

class AssignTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private LogRepositoryInterface $logRepository
    ) {}

    public function execute(Ticket $ticket, ?int $agentId, int $userId)
    {
        
        $currentAgent = $ticket->agent;
        $assigner = User::find($userId);
        
        if($agentId){
            $agent = $this->ticketRepository->findAgent($agentId);
           if (!$agent || !$agent->isAgent()) {
            throw ValidationException::withMessages([
                'agent_id' => 'Selected user is not an agent.',
            ]);
        }
            
        }
        $this->ticketRepository->assign($ticket,$agentId);
        
        $message = $agentId 
            ? "Ticket assigned to agent: {$agent->name}" 
            : ($currentAgent ? "Ticket unassigned from agent: {$currentAgent->name}" : "Ticket unassigned from agent");

        $this->logRepository->create([
            'ticket_id' => $ticket->id,
            'user_id'   => $userId,
            'action'    => 'assignment_changed',
            'description' => $message,
        ]);

        // Send notification when assigning to an agent
        if ($agentId && $agent && $assigner) {
            $updatedTicket = $this->ticketRepository->getTicketById($ticket->id);
            
            // Use SSE notification system (handles both database + cache)
            \App\Http\Controllers\NotificationController::handleTicketAssigned(
                $updatedTicket->id,
                $agent->id,
                $assigner->id,
                [
                    'title' => $updatedTicket->title,
                    'status' => $updatedTicket->status,
                    'priority' => $updatedTicket->priority,
                    'description' => $updatedTicket->description
                ]
            );
        }

        return $agentId ? 'Ticket assigned successfully!' : 'Ticket unassigned successfully!';
    }
}