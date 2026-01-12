<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Models\Comment;
use App\Models\Log;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a regular user
        $user = User::where('role', 'user')->first();
        
        if (!$user) {
            return; // No regular user exists
        }

        // Get some categories
        $techSupportCategory = Category::where('name', 'Technical Support')->first();
        $bugReportCategory = Category::where('name', 'Bug Report')->first();
        $featureRequestCategory = Category::where('name', 'Feature Request')->first();

        // Create sample tickets
        $ticket1 = Ticket::create([
            'title' => 'Login page not loading properly',
            'description' => 'When I try to access the login page, it takes forever to load and sometimes shows a blank page. This has been happening for the past two days.',
            'status' => 'open',
            'priority' => 'high',
            'user_id' => $user->id,
        ]);

        if ($techSupportCategory) {
            $ticket1->categories()->attach($techSupportCategory->id);
        }

        // Add a log entry
        Log::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $user->id,
            'action' => 'Ticket created by user'
        ]);

        $ticket2 = Ticket::create([
            'title' => 'Feature request: Dark mode',
            'description' => 'It would be great to have a dark mode option in the application. Many users prefer dark themes, especially when working late hours.',
            'status' => 'open',
            'priority' => 'medium',
            'user_id' => $user->id,
        ]);

        if ($featureRequestCategory) {
            $ticket2->categories()->attach($featureRequestCategory->id);
        }

        // Add a comment
        Comment::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $user->id,
            'content' => 'This would really improve the user experience, especially for developers who work in low-light environments.'
        ]);

        // Add a log entry
        Log::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $user->id,
            'action' => 'Ticket created by user'
        ]);

        Log::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $user->id,
            'action' => 'User added a comment'
        ]);

        $ticket3 = Ticket::create([
            'title' => 'Email notifications not working',
            'description' => 'I am not receiving email notifications for ticket updates. I have checked my spam folder and email settings.',
            'status' => 'in_progress',
            'priority' => 'medium',
            'user_id' => $user->id,
        ]);

        if ($bugReportCategory) {
            $ticket3->categories()->attach($bugReportCategory->id);
        }

        // Add log entries
        Log::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $user->id,
            'action' => 'Ticket created by user'
        ]);

        Log::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $user->id,
            'action' => 'Ticket status changed to In Progress'
        ]);
    }
}