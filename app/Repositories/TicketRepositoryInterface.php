<?php

namespace App\Repositories;

use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// interface TicketRepositoryInterface
// {
//     public function getUserTickets(int $userId, int $perPage = 10): LengthAwarePaginator;
    
//     public function getTicketById(int $ticketId): ?Ticket;
    
//     public function create(array $data): Ticket;
    
//     public function getCategories(): Collection;
    
//     public function getLabels(): Collection;
// }

interface TicketRepositoryInterface{
    public function getUserTickets(int $userId, int $perPage = 10);
    public function getAgentTickets(int $agentId, int $perPage = 10);
    public function getAllTickets(int $userId, int $perPage = 10);
    public function getTicketById(int $ticketId);
    public function getCategories();
    public function getLabels();
    public function create(array $data);
    public function update(array $data, int $ticketId);
    public function delete(Ticket $ticket);
    public function assign(Ticket $ticket, ?int $agentId);
    public function findAgent(int $id);
}