<?php

namespace Dskripchenko\OrchidLoggable\Services;

use Dskripchenko\OrchidLoggable\Models\ChangeLog;
use Orchid\Platform\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Psr\Log\LogLevel;
use Throwable;

class ChangeLogService
{
    /**
     * @var User|null
     */
    protected ?User $user;

    /**
     * @var Model|null
     */
    protected ?Model $entity;

    /**
     * @var bool
     */
    protected static bool $blind = false;

    /**
     * @param bool $blind
     *
     * @return void
     */
    public static function blind(bool $blind = true): void
    {
        static::$blind = $blind;
    }

    /**
     * @param User|null $user
     * @param Model|null $entity
     * @param string $message
     * @param string $level
     *
     * @return ChangeLog
     */
    public function log(?User $user, ?Model $entity, string $message, string $level = LogLevel::INFO): ChangeLog
    {
        if (static::$blind) {
            return new ChangeLog();
        }
        return ChangeLog::log($user, $entity, $message, $level);
    }

    /**
     * @param string $message
     * @param string $level
     *
     * @return ChangeLog
     */
    public function message(string $message, string $level = LogLevel::INFO): ChangeLog
    {
        return $this->log($this->user, $this->entity, $message, $level);
    }

    /**
     * @param User|null $user
     *
     * @return void
     */
    public function withUser(?User $user): void
    {
        $this->user = $user;
    }

    public function withEntity(?Model $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function beautify($value): string
    {
        try {
            if ($value instanceof Collection) {
                return $this->beautify($value->all());
            }

            if (empty($value)) {
                return "<span style='color: red'>empty</span>";
            }

            if (is_array($value)) {
                $json = json_encode(
                    $value,
                    JSON_THROW_ON_ERROR
                    | JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                );
                return "<pre>$json</pre>";
            }

            if (json_validate($value)) {
                $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                $json = json_encode(
                    $data,
                    JSON_THROW_ON_ERROR
                    | JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                );
                return "<pre>$json</pre>";
            }

            return (string) $value;
        } catch (Throwable) {
            return (string) $value;
        }
    }
}
