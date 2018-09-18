<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

class Event
{
    /**
     * Trigger insert event.
     *
     * @var string
     */
    const INSERT_EVENT = 'insert';

    /**
     * Trigger update event.
     *
     * @var string
     */
    const UPDATE_EVENT = 'update';

    /**
     * Trigger delete event.
     *
     * @var string
     */
    const DELETE_EVENT = 'delete';

    /**
     * Get trigger insert event.
     *
     * @return string
     */
    public static function insert()
    {
        return self::INSERT_EVENT;
    }

    /**
     * Get trigger update event.
     *
     * @var string
     */
    public static function update()
    {
        return self::UPDATE_EVENT;
    }

    /**
     * Get trigger delete event.
     *
     * @var string
     */
    public static function delete()
    {
        return self::DELETE_EVENT;
    }
}
