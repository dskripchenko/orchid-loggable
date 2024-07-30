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

            $details[] = <<<RAW_STR
The <b style='color: red'>$key</b> attribute value changed
<br>
<b>с : </b>
<div style='color: #1a88ff'>$oldValue</div>
<b>на : </b>
<div  style='color: #53b96a'>$value</div>
RAW_STR;
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
        $message = <<<RAW_STR
The entity has been added <br/>
$changes
RAW_STR;

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
        $message = <<<RAW_STR
The entity has been updated <br/>
$changes
RAW_STR;

        $this->service->log($this->getUser(), $entity, $message);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function deleted(Model $entity): void
    {
        $message = <<<RAW_STR
The entity has been deleted
RAW_STR;

        $this->service->log($this->getUser(), $entity, $message, LogLevel::WARNING);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function restored(Model $entity): void
    {
        $message = <<<RAW_STR
The entity has been restored
RAW_STR;

        $this->service->log($this->getUser(), $entity, $message);
    }

    /**
     * @param Model $entity
     *
     * @return void
     */
    public function forceDeleted(Model $entity): void
    {
        $message = <<<RAW_STR
Entity has been permanently deleted
RAW_STR;

        $this->service->log($this->getUser(), $entity, $message, LogLevel::CRITICAL);
    }
}
