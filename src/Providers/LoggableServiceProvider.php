<?php

namespace Dskripchenko\OrchidLoggable\Providers;

use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;

class LoggableServiceProvider extends ServiceProvider
{
    /**
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        $dashboard->registerPermissions(
            ItemPermission::group(__('Change Logs'))
            ->addPermission('platform.change_logs', __('Log records'))
        );

        $this->loadViewsFrom(dirname(__DIR__, 2) . '/views', 'change_logs');
        $this->loadMigrationsFrom(dirname(__DIR__, 2) . '/migrations');
    }
}
