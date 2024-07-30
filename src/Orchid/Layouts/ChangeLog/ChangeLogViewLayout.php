<?php

namespace Dskripchenko\OrchidLoggable\Orchid\Layouts\ChangeLog;

use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Orchid\Screen\Layouts\Content;

class ChangeLogViewLayout extends Content
{
    /**
     * @var string
     */
    protected $target = 'change_logs';

    protected $template = 'platform::layouts.block';

    public function render(ChangeLog $log): string
    {
        return view('change_logs::detail-info', [
            'details' => $log->details,
        ])->render();
    }
}
