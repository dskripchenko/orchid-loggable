# orchid-loggable

Change-log / audit trail for [Orchid](https://orchid.software/) platform models.
Records create / update / delete changes and ships Orchid screens to browse them.

> 🌐 **English** · [Deutsch](docs/de/README.md) · [Русский](docs/ru/README.md) · [中文](docs/zh/README.md)

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/orchid-loggable)](https://packagist.org/packages/dskripchenko/orchid-loggable)
[![License](https://img.shields.io/packagist/l/dskripchenko/orchid-loggable)](LICENSE.md)

## Requirements

PHP 8.2–8.5 · Laravel 11 / 12 / 13 · Orchid Platform 14 ·
`dskripchenko/orchid-extra` ^2.2.

## Install

```bash
composer require dskripchenko/orchid-loggable
php artisan migrate
```

The service provider is auto-discovered and registers the change-log screens in
your Orchid dashboard.

## Usage

Make a model auditable by implementing `LoggableEntity` and attaching
`ChangeLogObserver`:

```php
use Dskripchenko\OrchidLoggable\Contracts\LoggableEntity;
use Dskripchenko\OrchidLoggable\Observers\ChangeLogObserver;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements LoggableEntity
{
    // How each change-log entry is labelled…
    public function getLoggableTitle(): string
    {
        return $this->title;
    }

    // …and where it links back to.
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

Changes are stored as `ChangeLog` records and viewable through the package's
Orchid list / view screens. `getLoggableTitle()` / `getLoggableUrl()` control how
each entry is titled and linked in that UI.

## License

[MIT](LICENSE.md) © Denis Skripchenko
