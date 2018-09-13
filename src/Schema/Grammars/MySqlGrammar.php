<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars;

use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint;

class MySqlGrammar extends Grammar
{
    /**
     * List of table events.
     *
     * @var array
     */
    public $events = ['insert', 'update', 'delete'];

    /**
     * Possible list of trigger times.
     *
     * @var array
     */
    public $actionTimes = ['after', 'before'];

    /**
     * Compile create trigger clause.
     *
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @param Connection $connection
     * 
     * @return string
     */
    public function compileCreate(Blueprint $blueprint)
    {
        return ("CREATE TRIGGER {$blueprint->trigger} {$this->validateActionTiming($blueprint)} {$this->validateEvent($blueprint)} ON `{$blueprint->eventObjectTable}` FOR EACH ROW BEGIN {$blueprint->statement} END");  
    }

    /**
     * Validate event.
     *
     * @param string $event
     * 
     * @return string
     */
    private function validateEvent(Blueprint $blueprint)
    {
        if (! in_array(strtolower($blueprint->event), $this->events)) {
            throw new InvalidArgumentException("Cannot use {$blueprint->event} as trigger event.");
        }

        return $blueprint->event;
    }

    /**
     * Validate action time.
     * 
     * @param string $actionTime
     * 
     * @return string
     */
    private function validateActionTiming(Blueprint $blueprint)
    {
        if (! in_array(strtolower($blueprint->actionTiming), $this->actionTimes)) {
            throw new InvalidArgumentException("Cannot use {$blueprint->actionTiming} as trigger action timing.");
        }

        return $blueprint->actionTiming;
    }

    /**
     * Drop trigger.
     *
     * @param string $triggerTable
     * @param string $triggerName
     * 
     * @return string
     */
    public function compileDropIfExists(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        return sprintf("drop trigger if exists %s", $blueprint->trigger);
    }
}