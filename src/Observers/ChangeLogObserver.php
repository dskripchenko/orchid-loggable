<?php

namespace Dskripchenko\OrchidLoggable\Observers;

use Orchid\Platform\Models\User;
use Dskripchenko\OrchidLoggable\Services\ChangeLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Psr\Log\LogLevel;

class ChangeLogObserver
{
    /**
     * @param ChangeLogService $service
     */
    public function __construct(protected ChangeLogService $service)
    {
        //keep
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if (!($user instanceof User) && !is_subclass_of($user, User::class)) {
            return null;
        }

        return $user;
    }

    /**
     * @param Model $entity
     *
     * @return array
     */
    protected function getChangedAttributes(Model $entity): array
    {
        $untrackedFields = ['created_at', 'updated_at', 'deleted_at', 'remember_token'];
        $attributes = $entity->getAttributes();
        foreach ($untrackedFields as $field) {
            if (isset($attributes[$field])) {
                unset($attributes[$field]);
            }
        }
        return $attributes;
    }

    /**
     * @param Model $entity
     *
     * @return string
     */
    protected function getEntityChanges(Model $entity): string
    {
        $details = [];
        $attributes = $this->getChangedAttributes($entity);
        foreach ($attributes as $key => $value) {
            $value = (string) $value;
            $oldValue = (string) $entity->getRawOriginal($key);
            if ($value === $oldValue) {
                continue;
            }
            $oldValue = $this->service->beautify($oldValue);
            $value = $this->service->beautify($value);

            $details[] = __('The') . "
<b style='color: red'>$key</b>" . __('attribute has been changed') . "
<br>
<b>" . __('from') . " : </b>
<div style='color: #1a88ff'>$oldValue</div>
<b>" . __('to') . " : </b>
<div  style='color: #53b96a'>$value</div>
";
        }
        return implode('<hr/>', $details);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function created(Model $entity): void
    {
        $changes = $this->getEntityChanges($entity);
        $message = __('The entity has been added') . " <br/> $changes";

        $this->service->log($this->getUser(), $entity, $message, LogLevel::NOTICE);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function updated(Model $entity): void
    {
        $changes = $this->getEntityChanges($entity);
        if (!$changes) {
            return;
        }
        $message = __('The entity has been updated') . "<br/> $changes";

        $this->service->log($this->getUser(), $entity, $message);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function deleted(Model $entity): void
    {
        $message = __('The entity has been deleted');

        $this->service->log($this->getUser(), $entity, $message, LogLevel::WARNING);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function restored(Model $entity): void
    {
        $message = __('The entity has been restored');

        $this->service->log($this->getUser(), $entity, $message);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function forceDeleted(Model $entity): void
    {
        $message = __('Entity has been permanently deleted');

        $this->service->log($this->getUser(), $entity, $message, LogLevel::CRITICAL);
    }
}
