<?php
namespace App\Actions\Ticket;

use App\Repositories\LogRepositoryInterface;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateTicketAction{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private LogRepositoryInterface $logRepository
        ){}
    public function execute(array $data, $ticketId) {
        $ticketData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => $data['status']
        ];
        $ticket = $this->ticketRepository->update($ticketData, $ticketId);
        if(!empty($data['categories'])){
            $ticket->categories()->sync($data['categories']);
        }
        if(!empty($data['labels'])){
            $ticket->labels()->sync($data['labels']);
        }
         $this->logRepository->create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::user()->id,
            'action'    => 'update',
            'description' => "Ticket updated successfully!",
        ]);
        return $ticket;
    }
}