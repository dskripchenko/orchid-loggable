<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidLoggable\Orchid\Layouts\ChangeLog;

use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Dskripchenko\OrchidExtra\Layouts\BaseListLayout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

class ChangeLogListLayout extends BaseListLayout
{
    /**
     * @var string
     */
    public $target = 'change_logs';

    /**
     * @param  Model  $model
     * @return string|null
     */
    public function getEditRoute(Model $model): ?string
    {
        return null;
    }

    /**
     * @param Model $model
     *
     * @return string|null
     */
    public function getDetailRoute(Model $model): ?string
    {
        return route('platform.change_logs.view', $model);
    }

    /**
     * @return TD[]
     */
    public function getColumns(): array
    {
        return [
            TD::make('id', __('ID'))
                ->sort()
                ->defaultHidden(),

            TD::make('level', __('Level'))
                ->render(fn(ChangeLog $log) =>
                    Link::make(mb_strtoupper($log->level))
                        ->type($log->color)
                        ->href("?level={$log->level}")
                        ->set('turbo', false))
                ->sort(),

            TD::make('user_id', __('User'))
                ->render(fn(ChangeLog $log) => $log->user
                    ? $log->user->getKey()
                    : __('Not set'))
                ->sort(),

            TD::make('entity_type', __('Entity Type'))
                ->render(fn(ChangeLog $log) => $log->entity
                    ? Link::make(Str::limit($log->entity->getLoggableTitle()))
                        ->href($log->entity->getLoggableUrl())
                        ->target('_blank')
                    : $log->entity_description)

                ->sort(),

            TD::make('message', __('Message'))
                ->render(fn(ChangeLog $log) => Str::limit(Arr::first(explode('<br/>', (string) $log->message)), 50))
                ->sort(),
        ];
    }
}
