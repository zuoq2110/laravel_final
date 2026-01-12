<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\LabelController as AdminLabelController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth','role:admin')->group(function(){
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
});
// Admin routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('tickets', [
        TicketController::class,
        'index'
    ])->name('tickets.index');
    Route::get('tickets/create',[
        TicketController::class,
        'create'
    ])->name('tickets.create');
    Route::post('tickets',[
        TicketController::class,
        'store'
    ])->name('tickets.store');
    Route::get('tickets/{ticket}',[
        TicketController::class,
        'show'
    ])->name('tickets.show');
    Route::get('tickets/{ticket}/edit',[
        TicketController::class,
        'edit'
    ])->name('tickets.edit');
    Route::put('tickets/{ticket}',[
        TicketController::class,
        'update'
    ])->name('tickets.update');
    Route::delete('tickets/{ticket}',[
        TicketController::class,
        'destroy'
    ])->name('tickets.destroy');
    Route::post('tickets/{ticket}/comments',[
        TicketController::class,
        'storeComment'
    ])->name('tickets.comments.store');
    
    // Attachment download route
    Route::get('tickets/attachments/{attachment}/download', [
        TicketController::class,
        'downloadAttachment'
    ])->name('tickets.attachments.download');
    
    // Admin-only routes for ticket management
    Route::middleware('role:admin')->group(function () {
        Route::patch('tickets/{ticket}/assign', [
            TicketController::class,
            'assignAgent'
        ])->name('tickets.assign');
    });

});

// Admin routes - User Management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', AdminUserController::class);
    Route::resource('categories', AdminCategoryController::class)->names('categories');
    Route::resource('labels', AdminLabelController::class)->names('labels');
    Route::get('logs', [AdminLogController::class, 'index'])->name('logs.index');
});

// Agent routes - placeholder routes for menu items  
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/agent/tickets', function () {
        // Placeholder for agent tickets view
        return redirect()->route('tickets.index');
    })->name('agent.tickets');
});

require __DIR__.'/auth.php';
