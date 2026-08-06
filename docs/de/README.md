# orchid-loggable

Änderungsprotokoll für Modelle der [Orchid](https://orchid.software/)-Plattform:
zeichnet Anlegen, Ändern und Löschen auf und bringt Orchid-Screens mit, um darin
zu blättern.

> 🌐 [English](../../README.md) · **Deutsch** · [Русский](../ru/README.md) · [中文](../zh/README.md)

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/orchid-loggable)](https://packagist.org/packages/dskripchenko/orchid-loggable)
[![License](https://img.shields.io/packagist/l/dskripchenko/orchid-loggable)](../../LICENSE.md)

## Voraussetzungen

PHP 8.2–8.5 · Laravel 11 / 12 / 13 · Orchid Platform 14 ·
`dskripchenko/orchid-extra` ^2.2.

## Installation

```bash
composer require dskripchenko/orchid-loggable
php artisan migrate
```

Der Service Provider wird automatisch erkannt und registriert die
Protokoll-Screens in Ihrem Orchid-Dashboard.

## Verwendung

Damit ein Modell protokolliert wird, implementiert es `LoggableEntity` und
bekommt den `ChangeLogObserver`:

```php
use Dskripchenko\OrchidLoggable\Contracts\LoggableEntity;
use Dskripchenko\OrchidLoggable\Observers\ChangeLogObserver;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements LoggableEntity
{
    // Wie ein Protokolleintrag beschriftet wird …
    public function getLoggableTitle(): string
    {
        return $this->title;
    }

    // … und wohin er zurückführt.
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

Änderungen werden als `ChangeLog`-Datensätze abgelegt und über die Listen- und
Detail-Screens des Pakets angezeigt. `getLoggableTitle()` und
`getLoggableUrl()` bestimmen, wie ein Eintrag dort heißt und worauf er verlinkt.

## Lizenz

[MIT](../../LICENSE.md) © Denis Skripchenko
