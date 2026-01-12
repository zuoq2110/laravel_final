<?php

namespace App\Actions\Ticket;

use App\Repositories\TicketRepositoryInterface;

class GetUserTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ){}
    
    public function execute(int $ticketId){
        return $this->ticketRepository->getTicketById($ticketId);
    }
}