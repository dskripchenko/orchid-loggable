<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidLoggable\Orchid\Screens\ChangeLog;

use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Dskripchenko\OrchidLoggable\Orchid\Layouts\ChangeLog\ChangeLogViewLayout;
use Dskripchenko\OrchidExtra\Screens\BaseEditScreen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Orchid\Screen\Action;

class ChangeLogViewScreen extends BaseEditScreen
{
    /**
     * @var string|null
     */
    public ?string $name = 'User Action Audit';

    /**
     * @var string|null
     */
    public ?string $description = 'Details on the action';

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
     * @return ChangeLogViewLayout
     */
    protected function getEditLayoutClass()
    {
        return new ChangeLogViewLayout($this->getCurrentEntity());
    }

    /**
     * @param  Model  $entity
     * @param  Request  $request
     * @return array
     */
    protected function getSaveEntityRules(Model $entity, Request $request): array
    {
        return [];
    }

    /**
     * @return array|Action[]
     */
    public function commandBar(): array
    {
        return [];
    }
}
