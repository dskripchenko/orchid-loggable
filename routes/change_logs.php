<?php

use Dskripchenko\OrchidLoggable\Orchid\Screens\ChangeLog\ChangeLogListScreen;
use Dskripchenko\OrchidLoggable\Orchid\Screens\ChangeLog\ChangeLogViewScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

$entity = 'change_logs';
$parameter = 'change_log';
$baseRouteName = "platform.{$entity}";
$listScreen = ChangeLogListScreen::class;
$viewScreen = ChangeLogViewScreen::class;

Route::screen("{$entity}/{{$parameter}}/view", $viewScreen)
    ->name("{$baseRouteName}.view")
    ->breadcrumbs(function (Trail $trail) use ($baseRouteName) {
        return $trail
            ->parent($baseRouteName)
            ->push('View', '#');
    });

Route::screen($entity, $listScreen)
    ->name($baseRouteName)
    ->breadcrumbs(function (Trail $trail) use ($baseRouteName) {
        return $trail
            ->parent('platform.index')
            ->push('List', route($baseRouteName));
    });
