<?php

namespace Dskripchenko\OrchidLoggable\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;

class LoggableRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @return void
     */
    public function map(): void
    {
        if (file_exists(base_path('routes/platform.php'))) {
            Route::domain((string) config('platform.domain'))
                ->prefix(Dashboard::prefix('/'))
                ->middleware(config('platform.middleware.private'))
                ->group(dirname(__DIR__, 2) . '/routes/change_logs.php');
        }
    }
}
