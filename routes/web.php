<?php

use App\Livewire\Homepage;
use App\Livewire\MyMeetings;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/my-meetings', MyMeetings::class)->name('my-meetings');
});
