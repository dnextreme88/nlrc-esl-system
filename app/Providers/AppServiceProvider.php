<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // REF: https://www.luckymedia.dev/blog/refactoring-to-carbon-macros
        Carbon::macro('toUserTimezone', fn (): Carbon => $this->tz(auth()->user()?->timezone ?? config('app.timezone')));
    }
}
