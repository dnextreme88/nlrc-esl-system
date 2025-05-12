<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        KnowledgeBasePanel::configureUsing(
            fn (KnowledgeBasePanel $panel) => $panel
                ->brandName(config('app.name', 'NLRC-ESL'). ' Docs')
                ->disableBreadcrumbs()
                ->viteTheme('resources/css/filament-admin-theme.css')
        );
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
