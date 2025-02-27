<?php

use App\Livewire\Announcements\AnnouncementDetail;
use App\Livewire\Announcements\AnnouncementList;
use App\Livewire\Homepage;
use App\Livewire\MyMeetings;
use App\Livewire\NotificationList;
use App\Livewire\ReservationCalendar;
use App\Livewire\Settings\SecuritySettings;
use App\Livewire\Settings\SettingsPage;
use App\Livewire\Settings\TimeSettings;
use App\Livewire\Settings\UserSettings;
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
    Route::get('/notifications', NotificationList::class)->name('notifications');
    Route::get('/reservation-calendar', ReservationCalendar::class)->name('reservation-calendar');

    Route::group(['prefix' => 'announcements', 'as' => 'announcements.'], function () {
        Route::get('/', AnnouncementList::class)->name('index');
        Route::get('/{id}-{slug}', AnnouncementDetail::class)->name('detail');
    });

    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', SettingsPage::class)->name('index');
        Route::get('/security', SecuritySettings::class)->name('security');
        Route::get('/time', TimeSettings::class)->name('time');
        Route::get('/user', UserSettings::class)->name('user');
    });
});
