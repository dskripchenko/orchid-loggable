<?php

namespace Dskripchenko\OrchidLoggable\Orchid\Selections;

use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Orchid\Platform\Models\User;
use Dskripchenko\OrchidExtra\Facades\Selection as FilterFactory;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use Psr\Log\LogLevel;

class ChangeLogFilterLayout extends Selection
{
    public $template = self::TEMPLATE_LINE;

    /**
     * @return Filter[]
     */
    public function filters(): array
    {
        return [
            FilterFactory::input(__('ID'), 'id', '='),
            FilterFactory::selectFromOptions(__('Level'), 'level', [
                LogLevel::DEBUG => mb_strtoupper(LogLevel::DEBUG),
                LogLevel::INFO => mb_strtoupper(LogLevel::INFO),
                LogLevel::NOTICE => mb_strtoupper(LogLevel::NOTICE),
                LogLevel::WARNING => mb_strtoupper(LogLevel::WARNING),
                LogLevel::ERROR => mb_strtoupper(LogLevel::ERROR),
                LogLevel::CRITICAL => mb_strtoupper(LogLevel::CRITICAL),
                LogLevel::ALERT => mb_strtoupper(LogLevel::ALERT),
                LogLevel::EMERGENCY => mb_strtoupper(LogLevel::EMERGENCY),
            ]),
            FilterFactory::selectFromQuery(__('User'), 'user_id', User::query()),
            FilterFactory::selectFromOptions(__('Entity Type'), 'entity_type', ChangeLog::getAvailableLoggableEntitiesMap()),
            FilterFactory::input(__('Message'), 'message'),
            FilterFactory::date(__('Created At'), 'created_at'),
        ];
    }
}
