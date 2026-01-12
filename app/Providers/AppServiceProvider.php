<?php

namespace App\Providers;

use App\Repositories\TicketRepositoryInterface;
use App\Repositories\TicketRepository;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\CommentRepository;
use App\Repositories\LogRepository;
use App\Repositories\LogRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(LogRepositoryInterface::class, LogRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
