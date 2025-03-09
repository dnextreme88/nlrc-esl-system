<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

it('converts default UTC date to user timezone', function () {
    // Create user and login
    $user = User::factory()->make(['timezone' => 'Asia/Manila']);
    Auth::login($user);

    // Set default timezone in config
    config()->set('app.timezone', 'UTC');

    // Create a Carbon instance
    $date = Carbon::now('UTC');

    // Apply macro
    $date_to_user_timezone = $date->toUserTimezone();

    // Check if timezone is converted correctly
    expect($date_to_user_timezone->timezoneName)
        ->toBe($user->timezone);
});
