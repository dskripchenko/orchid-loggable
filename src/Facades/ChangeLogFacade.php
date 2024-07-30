<?php

namespace Dskripchenko\OrchidLoggable\Facades;

use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Orchid\Platform\Models\User;
use Dskripchenko\OrchidLoggable\Services\ChangeLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Psr\Log\LogLevel;

/**
 * @method static ChangeLog log(?User $user, ?Model $entity, string $message, string $level = LogLevel::INFO)
 * @method static ChangeLog message(string $message, string $level = LogLevel::INFO)
 * @method static void withUser(?User $user)
 * @method static void withEntity(?Model $entity)
 * @method static string beautify($value)
 */
class ChangeLogFacade extends Facade
{
    /**
     * @see ChangeLogService
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ChangeLogService::class;
    }
}
