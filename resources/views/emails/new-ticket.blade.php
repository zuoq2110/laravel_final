<x-mail::message>
# New Ticket Created

A new support ticket has been created and requires your attention.

**Ticket Details:**
- **Ticket ID:** #{{ $ticket->id }}
- **Title:** {{ $ticket->title }}
- **Priority:** {{ ucfirst($ticket->priority) }}
- **Status:** {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
- **Created by:** {{ $ticket->user->name }} ({{ $ticket->user->email }})
- **Created at:** {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}

**Description:**
{{ $ticket->description }}

<x-mail::button :url="$editUrl">
Edit Ticket
</x-mail::button>

You can also [view the ticket details]({{ $viewUrl }}) if you prefer.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
