<?php

namespace App\Actions\Ticket;

use App\Models\Comment;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\LogRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AddCommentToTicketAction
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private LogRepositoryInterface $logRepository
    ) {}

    public function execute(int $ticketId, string $content): Comment
    {
        $user = Auth::user();
        $commentData = [
            'content' => $content,
            'user_id' => $user->id,
            'ticket_id' => $ticketId
        ];
        $comment = $this->commentRepository->create($commentData);
        
        $this->logRepository->create([
            'ticket_id' => $ticketId,
            'user_id' => $user->id,
            'action'    => 'create',
            'description' => "Commented by {$user->name}",
        ]);
        return $comment;
    }
}