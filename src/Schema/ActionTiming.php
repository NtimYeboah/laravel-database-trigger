<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

class ActionTiming
{
    /**
     * After action timing.
     *
     * @var string
     */
    const ACTION_TIMING_AFTER = 'after';

    /**
     * Before action timing.
     *
     * @var string
     */
    const ACTION_TIMING_BEFORE = 'before';

    /**
     * Get after action timing.
     *
     * @return string
     */
    public static function after()
    {
        return self::ACTION_TIMING_AFTER;
    }

    /**
     * Get before action timing.
     *
     * @return string
     */
    public static function before()
    {
        return self::ACTION_TIMING_BEFORE;
    }
}
