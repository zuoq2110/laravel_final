<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CommentPolicy;
use App\Policies\LabelPolicy;
use App\Policies\TicketPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Ticket::class => TicketPolicy::class,
        Comment::class => CommentPolicy::class,
        Category::class => CategoryPolicy::class,
        Label::class => LabelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates for role-based permissions
        $this->defineRoleGates();
        
        // Define Gates for specific actions
        $this->defineActionGates();
    }

    /**
     * Define role-based gates.
     */
    private function defineRoleGates(): void
    {
        // Admin gate
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Agent gate
        Gate::define('agent', function (User $user) {
            return $user->isAgent();
        });

        // User gate
        Gate::define('user', function (User $user) {
            return $user->isUser();
        });

        // Agent or Admin gate
        Gate::define('agent-or-admin', function (User $user) {
            return $user->isAgent() || $user->isAdmin();
        });

        Gate::define('create', function (User $user) {
            return !$user->isAgent();
        });
    }

    /**
     * Define action-based gates.
     */
    private function defineActionGates(): void
    {
        // Dashboard access
        Gate::define('access-dashboard', function (User $user) {
            return $user->isAdmin() || $user->isAgent();
        });

        // Admin panel access
        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdmin();
        });
    }
}