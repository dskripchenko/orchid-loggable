<?php

namespace Dskripchenko\OrchidLoggable\Providers;

use Dskripchenko\OrchidLoggable\Facades\ChangeLogFacade;
use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Dskripchenko\OrchidLoggable\Observers\ChangeLogObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Orchid\Platform\Events\AddRoleEvent;
use Psr\Log\LogLevel;

class LoggableEventServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $loggableEntities = ChangeLog::getAvailableLoggableEntities();
        foreach ($loggableEntities as $entity) {
            $entity::observe(ChangeLogObserver::class);
        }

        Event::listen(AddRoleEvent::class, static function (AddRoleEvent $event) {
            $changes = ChangeLogFacade::beautify($event->roles);
            $message = "The user roles has been changed <br/> $changes";
            ChangeLogFacade::log(Auth::user(), $event->user, $message, LogLevel::WARNING);
        });
    }
}
