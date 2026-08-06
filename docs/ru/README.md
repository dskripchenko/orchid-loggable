# orchid-loggable

Журнал изменений моделей для платформы [Orchid](https://orchid.software/):
записывает создание, изменение и удаление и даёт экраны Orchid, чтобы всё это
просматривать.

> 🌐 [English](../../README.md) · [Deutsch](../de/README.md) · **Русский** · [中文](../zh/README.md)

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/orchid-loggable)](https://packagist.org/packages/dskripchenko/orchid-loggable)
[![License](https://img.shields.io/packagist/l/dskripchenko/orchid-loggable)](../../LICENSE.md)

## Требования

PHP 8.2–8.5 · Laravel 11 / 12 / 13 · Orchid Platform 14 ·
`dskripchenko/orchid-extra` ^2.2.

## Установка

```bash
composer require dskripchenko/orchid-loggable
php artisan migrate
```

Провайдер подхватывается автоматически и регистрирует экраны журнала в панели
Orchid.

## Использование

Чтобы модель попала в журнал, реализуйте `LoggableEntity` и повесьте
`ChangeLogObserver`:

```php
use Dskripchenko\OrchidLoggable\Contracts\LoggableEntity;
use Dskripchenko\OrchidLoggable\Observers\ChangeLogObserver;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements LoggableEntity
{
    // Как подписывается запись журнала…
    public function getLoggableTitle(): string
    {
        return $this->title;
    }

    // …и куда она ведёт.
    public function getLoggableUrl(): string
    {
        return route('platform.articles.edit', $this);
    }

    protected static function booted(): void
    {
        static::observe(ChangeLogObserver::class);
    }
}
```

Изменения складываются в записи `ChangeLog` и открываются на экранах списка и
просмотра из пакета. `getLoggableTitle()` и `getLoggableUrl()` определяют, как
запись названа и куда ведёт ссылка в этом интерфейсе.

## Лицензия

[MIT](../../LICENSE.md) © Денис Скрипченко
