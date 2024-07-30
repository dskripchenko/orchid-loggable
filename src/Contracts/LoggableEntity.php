<?php

namespace Dskripchenko\OrchidLoggable\Contracts;

interface LoggableEntity
{
    /**
     * @return string
     */
    public function getLoggableTitle(): string;

    /**
     * @return string
     */
    public function getLoggableUrl(): string;
}
