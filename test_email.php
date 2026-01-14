<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ticket;
use App\Mail\NewTicketNotification;
use Illuminate\Support\Facades\Mail;

try {
    // Get first ticket
    $ticket = Ticket::first();
    
    if (!$ticket) {
        echo "No ticket found. Please create a ticket first.\n";
        exit;
    }
    
    echo "Sending test email for ticket ID: " . $ticket->id . "\n";
    echo "From address: " . config('mail.from.address') . "\n";
    echo "To address: zuoq2110@gmail.com\n";
    echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "SMTP Username: " . config('mail.mailers.smtp.username') . "\n";
    
    // Send email directly (not queued)
    Mail::to('zuoq2110@gmail.com')->send(new NewTicketNotification($ticket));
    
    echo "Email sent successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}