<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketPurchaseController;
use App\Http\Middleware\OrganizerMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

// Route for unauthenticated users
Route::get('/', [HomeController::class, 'home'])->name('login');

Route::middleware([OrganizerMiddleware::class])->prefix('organizer')->name('organizer.')->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Event routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'organizerEvents'])->name('list');
        Route::get('/detail/{id}', [EventController::class, 'organizerEventDetails'])->name('detail');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/store', [EventController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [EventController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [EventController::class, 'update'])->name('update');
        Route::get('/attendee-list/{id?}', [EventController::class, 'getAttendees'])->name('attendee.list');
        Route::get('/export', [EventController::class, 'export'])->name('export');
    });
});

// Authenticated Routes (requires 'auth' middleware)
Route::middleware(['auth'])->group(function () {

    // Comment routes
    Route::post('/event/comments', [CommentController::class, 'store'])->name('comments.store');

    // Payment routes
    Route::post('/create-payment-intent', [TicketPurchaseController::class, 'createPaymentIntent'])->name('create.payment.intent');
    Route::post('/confirm-payment', [TicketPurchaseController::class, 'confirmPayment'])->name('confirm.payment');

    // Ticket routes
    Route::get('/ticket/details/{id?}', [TicketController::class, 'ticketDetails'])->name('ticket.details');
});

// Public Routes
Route::get('/home', [HomeController::class, 'home'])->name('home');
Route::post('/events/upcoming', [HomeController::class, 'loadUpcomingEvents'])->name('load.upcoming.events');
Route::get('/cities', [HomeController::class, 'getCities'])->name('get.cities');
Route::get('/event/detail/{id}', [EventController::class, 'details'])->name('events.details');
Route::get('/event/comments/{id?}', [CommentController::class, 'index'])->name('comments.index');
