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
    public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        if ($this->isInsertTriggerEvent($blueprint)) {
            return $this->getInsertClause($blueprint, $command, $connection);
        }

        if ($this->isUpdateTriggerEvent($blueprint)) {
            return $this->getUpdateClause($blueprint, $command, $connection);
        }

        return $this->getStatementClause($blueprint, $command, $connection);
    }

    /**
     * Determine trigger type.
     *
     * @param Blueprint $blueprint
     * 
     * @return boolean
     */
    private function isInsertTriggerEvent(Blueprint $blueprint)
    {
        return $blueprint->tableOperation === 'insert' && ! is_null($blueprint->table);
    }

    /**
     * Determine trigger type.
     * 
     * @param Blueprint $blueprint
     * 
     * @return boolean
     */
    private function isUpdateTriggerEvent(Blueprint $blueprint)
    {
        return $blueprint->tableOperation === 'update' && ! is_null($blueprint->table);
    }

    /**
     * Get trigger clause for statement method.
     *
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @param Connection $connection
     * 
     * @return string
     */
    private function getStatementClause(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        return sprintf('delimiter $$ create trigger %s %s %s on %s for each row begin %s; end$$ delimiter ;',
            $blueprint->trigger, $this->validateActionTime($blueprint), $this->validateEvent($blueprint), 
            $blueprint->eventTable, $blueprint->clause);
    }

    /**
     * Get trigger clause for insert.
     *
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @param Connection $connection
     * 
     * @return string
     */
    private function getInsertClause(Blueprint $blueprint, Fluent $command, Connection $connection)
    {   
        return sprintf('delimiter $$ create trigger %s %s %s on %s for each row begin %s into %s set %s end$$ delimiter ;', 
            $blueprint->trigger, $this->validateActionTime($blueprint), $this->validateEvent($blueprint), $blueprint->eventTable, 
            $blueprint->tableOperation, $blueprint->table, $blueprint->clause);
    }

    /**
     * Get trigger clause for update.
     * 
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @param Connection $connection
     * 
     * @return string
     */
    private function getUpdateClause(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        return sprintf('delimiter $$ create trigger %s %s %s on %s for each row begin %s %s set %s end$$ delimiter ;', 
            $blueprint->trigger, $this->validateActionTime($blueprint), $this->validateEvent($blueprint), $blueprint->eventTable, 
            $blueprint->tableOperation, $blueprint->table, $blueprint->clause);
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
            throw new InvalidArgumentException("Cannot use {$blueprint->event} as trigger event");
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
    private function validateActionTime(Blueprint $blueprint)
    {
        if (! in_array(strtolower($blueprint->time), $this->actionTimes)) {
            throw new InvalidArgumentException("Cannot use {$blueprint->time} as trigger action time");
        }

        return $blueprint->time;
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