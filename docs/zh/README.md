# orchid-loggable

面向 [Orchid](https://orchid.software/) 平台模型的变更日志：记录创建、更新与删除，
并附带用于浏览这些记录的 Orchid 界面。

> 🌐 [English](../../README.md) · [Deutsch](../de/README.md) · [Русский](../ru/README.md) · **中文**

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/orchid-loggable)](https://packagist.org/packages/dskripchenko/orchid-loggable)
[![License](https://img.shields.io/packagist/l/dskripchenko/orchid-loggable)](../../LICENSE.md)

## 环境要求

PHP 8.2–8.5 · Laravel 11 / 12 / 13 · Orchid Platform 14 ·
`dskripchenko/orchid-extra` ^2.2。

## 安装

```bash
composer require dskripchenko/orchid-loggable
php artisan migrate
```

服务提供者会被自动发现，并把变更日志界面注册到你的 Orchid 后台。

## 用法

让模型实现 `LoggableEntity` 并挂上 `ChangeLogObserver`，它就会被记录：

```php
use Dskripchenko\OrchidLoggable\Contracts\LoggableEntity;
use Dskripchenko\OrchidLoggable\Observers\ChangeLogObserver;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements LoggableEntity
{
    // 每条日志如何标注……
    public function getLoggableTitle(): string
    {
        return $this->title;
    }

    // ……以及它链接回哪里。
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

变更以 `ChangeLog` 记录保存，可通过包内的列表与详情界面查看。
`getLoggableTitle()` 与 `getLoggableUrl()` 决定每条记录在界面中的标题和跳转目标。

## 许可证

[MIT](../../LICENSE.md) © Denis Skripchenko
