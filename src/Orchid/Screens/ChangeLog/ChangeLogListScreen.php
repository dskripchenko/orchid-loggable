<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidLoggable\Orchid\Screens\ChangeLog;

use Dskripchenko\OrchidLoggable\Facades\ChangeLogFacade;
use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Dskripchenko\OrchidLoggable\Orchid\Layouts\ChangeLog\ChangeLogListLayout;
use Dskripchenko\OrchidLoggable\Orchid\Selections\ChangeLogFilterLayout;
use Dskripchenko\OrchidExtra\Screens\BaseListScreen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Facades\Alert;
use Psr\Log\LogLevel;

class ChangeLogListScreen extends BaseListScreen
{
    /**
     * @var string|null
     */
    public ?string $name = 'User Action Audit';

    /**
     * @var string|null
     */
    public ?string $description = 'List of logged user actions';

    /**
     * @var string|null
     */
    public ?string $permission = 'platform.change_logs';

    /**
     * @return Model
     */
    protected function entity(): Model
    {
        return new ChangeLog();
    }

    /**
     * @return string
     */
    protected function getListLayoutClass(): string
    {
        return ChangeLogListLayout::class;
    }

    /**
     * @return array
     */
    protected function getSelections(): array
    {
        return [
            ChangeLogFilterLayout::class,
        ];
    }

    /**
     * @return string[]
     */
    protected function getQueryWithParameters(): array
    {
        return ['user'];
    }


    public function deleting(Request $request): RedirectResponse
    {
        $id = $request->get('id');
        $entity = $this->entity()
            ->newQuery()
            ->findOrFail($id);

        ChangeLogFacade::log(Auth::user(), $entity, "Attempt to delete log record", LogLevel::CRITICAL);
        Alert::error('The entry will not be deleted.');

        return redirect()->back();
    }
}
