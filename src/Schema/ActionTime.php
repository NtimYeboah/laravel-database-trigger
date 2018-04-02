<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

class ActionTime
{   
    /**
     * After event
     * 
     * @var string
     */
    const ACTION_TIME_AFTER = 'after';

    /**
     * Before event
     * 
     * @var string
     */
    const ACTION_TIME_BEFORE = 'before';

    /**
     * Get after event
     * 
     * @return string
     */
    public static function after()
    {
        return self::ACTION_TIME_AFTER;
    }

    /**
     * Get before event
     * 
     * @return string
     */
    public static function before()
    {
        return self::ACTION_TIME_BEFORE;
    }
}