<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

use Closure;
use Illuminate\Database\Connection;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar;

class MySqlBuilder
{
    /**
     * Database connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The schema grammar instance.
     *
     * @var \NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar
     */
    protected $grammar;

    /**
     * Trigger name.
     *
     * @var string
     */
    protected $trigger;

    /**
     * Trigger event object table.
     *
     * @var string
     */
    protected $eventObjectTable;

    /**
     * Statements to execute for trigger.
     *
     * @var Closure
     */
    protected $callback;

    /**
     * Trigger action timing.
     *
     * @var string
     */
    protected $actionTiming;

    /**
     * Event to activate trigger.
     *
     * @var string
     */
    protected $event;

    /**
     * Create a new database Schema manager.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->grammar = $this->getDefaultGrammar();
    }

    /**
     * Create new trigger.
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function create($trigger)
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * Event object table.
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function on($eventObjectTable)
    {
        $this->eventObjectTable = $eventObjectTable;

        return $this;
    }

    /**
     * Trigger statement.
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function statement(Closure $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Trigger after action timing.
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function after()
    {
        $this->actionTiming = ActionTiming::after();

        return $this;
    }

    /**
     * Trigger before action timing.
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function before()
    {
        $this->actionTiming = ActionTiming::before();

        return $this;
    }

    /**
     * Trigger insert event.
     *
     * @return void
     */
    public function insert()
    {
        $this->event = Event::insert();

        $this->callBuild();
    }

    /**
     * Trigger update event.
     *
     * @return void
     */
    public function update()
    {
        $this->event = Event::update();

        $this->callBuild();
    }

    /**
     * Trigger delete event.
     *
     * @return void
     */
    public function delete()
    {
        $this->event = Event::delete();

        $this->callBuild();
    }

    /**
     * Determine if the given trigger exists.
     *
     * @param  string  $trigger
     * @return bool
     */
    public function hasTrigger($trigger)
    {
        return count($this->connection->select(
            $this->grammar->compileTriggerExists(),
            [$trigger]
        )) > 0;
    }

    /**
     * Drop trigger.
     *
     * @return void
     */
    public function dropIfExists($trigger)
    {
        $this->build(tap($this->createBlueprint($trigger), function ($blueprint) {
            $blueprint->dropIfExists();
        }));
    }

    /**
     * Get action timing.
     *
     * @return string
     */
    protected function getActionTiming()
    {
        return $this->actionTiming;
    }

    /**
     * Get event.
     *
     * @return string
     */
    protected function getEvent()
    {
        return $this->event;
    }

    /**
     * Get trigger event object table.
     *
     * @return string
     */
    protected function getEventObjectTable()
    {
        return $this->eventObjectTable;
    }

    /**
     * Get trigger statement.
     *
     * @return Closure
     */
    protected function getStatement()
    {
        return $this->callback;
    }

    /**
     * Call build to execute blueprint to build trigger.
     *
     * @return void
     */
    public function callBuild()
    {
        $eventObjectTable = $this->getEventObjectTable();
        $callback = $this->getStatement();
        $actionTiming = $this->getActionTiming();
        $event = $this->getEvent();

        $this->build(tap(
            $this->createBlueprint($this->trigger),
            function (Blueprint $blueprint) use ($eventObjectTable, $callback, $actionTiming, $event) {
                $blueprint->create();
                $blueprint->on($eventObjectTable);
                $blueprint->statement($callback);
                $blueprint->$actionTiming();
                $blueprint->$event();
            }
        ));
    }

    /**
     * Execute the blueprint to build trigger.
     *
     * @param  \NtimYeboah\LaravelDatabaseTriggers\Schema\Blueprint  $blueprint
     * @return void
     */
    protected function build(Blueprint $blueprint)
    {
        $blueprint->build($this->connection, $this->grammar);
    }

    /**
     * Create a new command set with a Closure.
     *
     * @param string $trigger
     * @param string $eventTable
     * @param Closure $callback
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint
     */
    protected function createBlueprint($trigger)
    {
        return new Blueprint($trigger);
    }

    /**
     * Get default schema grammar instance.
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultGrammar()
    {
        return new MySqlGrammar;
    }
}
