<?php

namespace App\Http\Controllers;

use App\Actions\Ticket\CreateTicketAction;
use App\Actions\Ticket\AddCommentToTicketAction;
use App\Actions\Ticket\AssignTicketAction;
use App\Actions\Ticket\GetUserTicketAction;
use App\Actions\Ticket\UpdateTicketAction;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Log;
use App\Models\User;
use App\Models\TicketAttachment;
use App\Repositories\TicketRepositoryInterface;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private CreateTicketAction $createTicketAction,
        private AddCommentToTicketAction $addCommentAction,
        private GetUserTicketAction $getUserTicketAction,
        private UpdateTicketAction $updateTicketAction,
        private AssignTicketAction $assignTicketAction,
        private CloudinaryService $cloudinaryService
    ) {}

    public function index()
    {
        $user = Auth::user();

        if($user->isAdmin()){
            $tickets = $this->ticketRepository->getAllTickets($user->id);
        }
        else if ($user->isAgent()) {
            $tickets = $this->ticketRepository->getAgentTickets($user->id);
        } else {
            $tickets = $this->ticketRepository->getUserTickets($user->id);
        }

        // $tickets = $this->ticketRepository->getUserTickets(Auth::id());

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $this->authorize('create', Ticket::class);
        
        $categories = $this->ticketRepository->getCategories();
        $labels = $this->ticketRepository->getLabels();
        return view('tickets.create', compact('categories', 'labels'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:open,in_progress,closed',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'attachments' => 'nullable|array|max:5', // Max 5 files
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,txt,zip,rar|max:10240' // Max 10MB per file
        ]);

        $ticket = $this->createTicketAction->execute($validated);


        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    // Upload to Cloudinary
                    $uploadResult = $this->cloudinaryService->uploadFile($file, 'tickets/' . $ticket->id);
                    // dd($uploadResult);
                    // Save attachment info to database
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'filename' => $file->getClientOriginalName(),
                        'cloudinary_public_id' => $uploadResult['public_id'],
                        'cloudinary_url' => $uploadResult['url'],
                        'cloudinary_secure_url' => $uploadResult['secure_url'],
                        'file_type' => $file->getMimeType(),
                        'file_size' => $uploadResult['bytes'],
                    ]);
                } catch (Exception $e) {
                    // Log the error but don't fail the ticket creation
                    Log::error('Failed to upload attachment: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket = $this->getUserTicketAction->execute($ticket->id);

        if (!$ticket) {
            abort(404, 'Ticket not found.');
        }

        // $agents = auth()->user()->isAdmin() ? User::where('role', 'agent')->get() : collect();
        $agents = null;
        if(Auth::user()->isAdmin()){
            $agents = User::where('role','agent')->get();
        }

        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function edit(Ticket $ticket)
    {
        $ticket = $this->getUserTicketAction->execute($ticket->id);
        
        $this->authorize('update', $ticket);

        $categories = $this->ticketRepository->getCategories();
        $labels = $this->ticketRepository->getLabels();
        return view('tickets.edit', compact('ticket', 'categories', 'labels'));
    }

    public function update(Request $request, $ticketId)
    {
        $ticket = $this->ticketRepository->getTicketById($ticketId);
        
        $this->authorize('update', $ticket);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'in:low,medium,high',
            'status' => 'in:open,in_progress,closed',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'labels' => 'array',
            'labels.*' => 'exists:labels,id',
            'comment' => 'nullable|string'
        ]);

        $ticket = $this->updateTicketAction->execute($validated, $ticketId);
        if (!empty($validated['comment'])) {
            $this->addCommentAction->execute($ticket->id, $validated['comment']);
        }
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket){
        $this->authorize('delete',$ticket);
        $this->ticketRepository->delete($ticket);
        return redirect()->route('tickets.index')
        ->with('success', 'Ticket deleted successfully!');
    }
    
    public function storeComment(Request $request, Ticket $ticket)
    {
        $this->authorize('createOnTicket', [Comment::class, $ticket]);

        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $this->addCommentAction->execute($ticket->id, $validated['content']);

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Assign ticket to an agent (Admin only)
     */
    // public function assignAgent(Request $request, Ticket $ticket)
    // {
    //     $this->authorize('assign', $ticket);

    //     $validated = $request->validate([
    //         'agent_id' => 'nullable|exists:users,id',
    //     ]);

    //     // If agent_id is provided, make sure the user is an agent
    //     if ($validated['agent_id']) {
    //         $agent = \App\Models\User::findOrFail($validated['agent_id']);
    //         if (!$agent->isAgent()) {
    //             return back()->withErrors(['agent_id' => 'Selected user is not an agent.']);
    //         }
    //     }

    //     $ticket->update([
    //         'agent_id' => $validated['agent_id'],
    //     ]);

    //     // Log the assignment change
    //     $message = $validated['agent_id'] 
    //         ? "Ticket assigned to agent: {$agent->name}"
    //         : "Ticket unassigned from agent";
            
    //     \App\Models\Log::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => auth()->id(),
    //         'action' => 'assignment_changed',
    //         'description' => $message,
    //     ]);

    //     $successMessage = $validated['agent_id'] 
    //         ? 'Ticket assigned successfully!'
    //         : 'Ticket unassigned successfully!';

    //     return back()->with('success', $successMessage);
    // }

    public function assignAgent(Request $request, Ticket $ticket){
        $this->authorize('assign', $ticket);
        $validated = $request->validate([
            'agent_id' => 'nullable|exists:users,id'
        ]);
        $user=Auth::user();
        
        $message=$this->assignTicketAction->execute($ticket, $validated['agent_id'], $user->id);
        return back()->with('success', $message);

    }

    public function downloadAttachment(TicketAttachment $attachment)
    {
        // Check if user can view the ticket that owns this attachment
        $this->authorize('view', $attachment->ticket);

        try {
            // Get file contents from Cloudinary
            $fileContents = file_get_contents($attachment->cloudinary_secure_url);
            
            if ($fileContents === false) {
                abort(404, 'File not found or could not be downloaded.');
            }

            // Return the file as a download response
            return response($fileContents, 200, [
                'Content-Type' => $attachment->file_type,
                'Content-Disposition' => 'attachment; filename="' . $attachment->filename . '"',
                'Content-Length' => $attachment->file_size,
            ]);

        } catch (Exception $e) {
            abort(404, 'File could not be downloaded.');
        }
    }
}
