<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('church-members', 'church-members.index')->name('church-members.index');
    Volt::route('church-members/create', 'church-members.create')->name('church-members.create');
    Volt::route('church-members/{churchMember}', 'church-members.show')->name('church-members.show');
    Volt::route('church-members/{churchMember}/edit', 'church-members.edit')->name('church-members.edit');
});

require __DIR__.'/auth.php';
