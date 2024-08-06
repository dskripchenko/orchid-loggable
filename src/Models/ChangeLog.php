<?php

namespace Dskripchenko\OrchidLoggable\Models;

use Dskripchenko\OrchidLoggable\Contracts\LoggableEntity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereBetween;
use Orchid\Platform\Concerns\Sortable;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\AsSource;
use Orchid\Support\Color;
use Psr\Log\LogLevel;
use RuntimeException;
use Throwable;

/**
 * @property integer $id
 * @property string $level
 * @property integer|null $user_id
 * @property string|null $entity_type
 * @property integer|null $entity_id
 * @property string|null $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 * @property-read LoggableEntity|null $entity
 * @property-read array $details
 * @property-read Color $color
 * @property-read string $entity_description
 */
class ChangeLog extends Model implements LoggableEntity
{
    use SoftDeletes;
    use AsSource;
    use Filterable;
    use Sortable;

    protected static array $availableEntities = [];

    /**
     * @var string[]
     */
    protected $fillable = [
        'level', 'user_id', 'entity', 'entity_id', 'message',
    ];

    /**
     * @var array
     */
    public array $allowedSorts = [
        'id', 'created_at', 'updated_at',
        'level', 'user_id', 'entity', 'entity_id', 'message',
    ];

    /**
     * @var array
     */
    public array $allowedFilters = [
        'id' => Where::class,
        'level' => Where::class,
        'user_id' => Where::class,
        'entity' => Where::class,
        'entity_id' => Where::class,
        'message' => Like::class,
        'created_at' => WhereBetween::class,
        'updated_at' => WhereBetween::class,
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return LoggableEntity|null
     */
    public function getEntityAttribute(): ?LoggableEntity
    {
        $isValidEntityType = $this->entity_type
            && class_exists($this->entity_type)
            && is_subclass_of($this->entity_type, LoggableEntity::class);

        if (!$isValidEntityType) {
            return null;
        }
        static $cache = [];
        $hash = md5("$this->entity_type $this->entity_id");
        if (isset($cache[$hash])) {
            return $cache[$hash];
        }

        /** @var Model $model */
        $model = app($this->entity_type);
        /** @var $entity LoggableEntity */
        $entity = $model->newModelQuery()->find($this->entity_id);
        $cache[$hash] = $entity;
        return $entity;
    }

    /**
     * @return LoggableEntity[]|Model[]
     */
    public static function getAvailableLoggableEntities(): array
    {
        $items = [];
        foreach (array_keys(static::getAvailableLoggableEntitiesMap()) as $entity) {
            if (is_subclass_of($entity, LoggableEntity::class)) {
                $items[] = $entity;
            }
        }
        return $items;
    }

    /**
     * @return array
     */
    public static function getAvailableLoggableEntitiesMap(): array
    {
        return static::$availableEntities;
    }

    public static function registerLoggableEntity(string $name, string $entity): void
    {
        if (!is_subclass_of($entity, LoggableEntity::class)) {
            throw new RuntimeException(
                sprintf(
                    __("Entity %s must be subclass of %s"),
                    $entity,
                    LoggableEntity::class
                )
            );
        }
        static::$availableEntities[$entity] = $name;
    }

    /**
     * @param User|null $user
     * @param Model|null $entity
     * @param string $message
     * @param string $level
     *
     * @return static
     */
    public static function log(?User $user, ?Model $entity, string $message, string $level = LogLevel::INFO): static
    {
        $log = new static();
        if ($user) {
            $log->user_id = $user->getKey();
        }

        if ($entity) {
            $log->entity_type = get_class($entity);
            $log->entity_id = $entity->getKey();
        }

        $log->message = $message;
        $log->level = $level;
        $log->save();
        return $log;
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function getDetailsAttribute(): array
    {
        $user = $this->user;
        $entity = $this->entity;
        return [
            'id' => $this->id,
            'Created At' => $this->created_at->format('d.m.Y H:i:s'),
            'Level' => mb_strtoupper($this->level),
            'User' => (
                $user
                    ? $user->getKey()
                    : __('Not set')
            ),
            'Title' => (
                $entity
                    ? Link::make($entity->getLoggableTitle())
                        ->withoutFormType()
                        ->target('_blank')
                        ->href($entity->getLoggableUrl())
                    : $this->entity_description
            ),
            'Message' => (
                $this->message
                    ? new HtmlString($this->message)
                    : __('Not set')
            ),
        ];
    }

    /**
     * @return Color
     */
    public function getColorAttribute(): Color
    {
        return match ($this->level) {
            LogLevel::EMERGENCY, LogLevel::CRITICAL, LogLevel::ERROR => Color::DANGER,
            LogLevel::WARNING, LogLevel::ALERT => Color::WARNING,
            LogLevel::INFO, LogLevel::DEBUG,LogLevel::NOTICE => Color::LIGHT,
            default => Color::PRIMARY
        };
    }

    /**
     * @return string
     */
    public function getEntityDescriptionAttribute(): string
    {
        $description = null;
        if ($this->entity_type) {
            $description = $this->entity_type;
        }

        if ($this->entity_id) {
            $description = trim("$description $this->entity_id");
        }

        if (!$description) {
            return __("Empty");
        }

        return $description;
    }

    /**
     * @return string
     */
    public function getLoggableTitle(): string
    {
        return __('Log record');
    }

    /**
     * @return string
     */
    public function getLoggableUrl(): string
    {
        return route('platform.change_logs.view', [
            'change_log' => $this->id,
        ]);
    }
}
