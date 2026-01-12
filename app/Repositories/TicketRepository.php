<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Label;
use App\Models\Log;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketRepository implements TicketRepositoryInterface
{
    public function getUserTickets(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Ticket::where('user_id', $userId)
            ->with('categories', 'comments', 'agent')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAllTickets(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Ticket::orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAgentTickets(int $agentId, int $perPage = 10): LengthAwarePaginator
    {
        return Ticket::where('agent_id',$agentId)
        ->with('categories','comments','user')
        ->orderBy('created_at','desc')
        ->paginate($perPage);
    }
    
    public function getTicketById(int $ticketId): Ticket
    {
        // return Ticket::with(['categories', 'labels', 'comments.user', 'logs', 'agent'])
        //     ->find($ticketId);
        return Ticket::where('id',$ticketId)
        ->with(['categories', 'labels','comments.user','logs','agent','attachments'])
        ->first();
    }
    
    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function update(array $data, int $ticketId): Ticket{
        $ticket = Ticket::find($ticketId);
        $ticket->update($data);
        return $ticket;
    }

    public function delete(Ticket $ticket){
        return DB::transaction(function () use($ticket){
        $user = Auth::user();
        
         Log::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'action'    => 'deleted',
            'description' => "Ticket has been deleted!",
        ]);
        return $ticket->delete();
        });  
    }
    
    public function getCategories(): Collection
    {
        return Category::all();
    }
    
    public function getLabels(): Collection
    {
        return Label::all();
    }

    public function assign(Ticket $ticket, ?int $agentId)
    {
       return  $ticket->update(['agent_id'=> $agentId]);
    }
    public function findAgent(int $id)
    {
        return User::find($id);
    }
}