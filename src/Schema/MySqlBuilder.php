<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

use Closure;
use Illuminate\Database\Connection;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Event;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint;
use NtimYeboah\LaravelDatabaseTrigger\Schema\ActionTime;
use NtimYeboah\LaravelDatabaseTrigger\Schema\QueryStatement;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar;

class MySqlBuilder
{
    /**
     * Database connection
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The schema grammar instance.
     *
     * @var \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected $grammar;

    /**
     * Trigger name
     * 
     * @var sting
     */
    protected $trigger;

    /**
     * Trigger event table
     *
     * @var string
     */
    protected $eventTable;

    /**
     * Statements to execute for trigger
     * 
     * @var Closure
     */
    protected $callback;

    /**
     * Trigger action time
     */
    protected $actionTime;

    /**
     * Trigger event
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

    public function create($trigger, $eventTable, Closure $callback)
    {
        $this->trigger = $trigger;
        $this->eventTable = $eventTable;
        $this->callback = $callback;

        return $this;
    }

    /**
     * Trigger after action time
     * 
     * @return NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function after()
    {
        $this->actionTime = ActionTime::after();

        return $this;
    }

    /**
     * Trigger before action time
     * 
     * @return NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder
     */
    public function before()
    {
        $this->actionTime = ActionTime::before();

        return $this;
    }

    /**
     * Trigger insert event
     *
     * @return void
     */
    public function insert()
    {
        $this->event = Event::insert();

        $this->callBuild();
    }

    /**
     * Trigger update event
     *
     * @return void
     */
    public function update()
    {
        $this->event = Event::update();

        $this->callBuild();
    }

    /**
     * Trigger delete event
     * 
     * @return void
     */
    public function delete()
    {
        $this->event = Event::delete();

        $this->callBuild();
    }

    /**
     * Drop trigger
     * 
     * @return void
     */
    public function dropIfExists($trigger)
    {
        (new Blueprint($trigger))->dropIfExists();
    }

    /**
     * Get action time
     * 
     * @return string
     */
    protected function getActionTime()
    {
        return $this->actionTime;
    }

    /**
     * Get event
     * 
     * @return string
     */
    protected function getEvent()
    {
        return $this->event;
    }

    /**
     * Call build to execute blueprint to build trigger
     * 
     * @return void
     */
    public function callBuild()
    {
        $eventTime = $this->getActionTime();
        $event = $this->getEvent();

        $this->build(tap($this->createBlueprint($this->trigger, $this->eventTable, $this->callback), 
            function (Blueprint $blueprint) use ($eventTime, $event) {
                $blueprint->create();
                $blueprint->$eventTime();
                $blueprint->$event();
            }
        ));
    }

    /**
     * Execute the blueprint to build trigger.
     *
     * @param  NtimYeboah\LaravelDatabaseTriggers\Schema\Blueprint  $blueprint
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
     * 
     * @return NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint
     */
    protected function createBlueprint($trigger, $eventTable, Closure $callback = null)
    {
        return new Blueprint($trigger, $eventTable, $callback);
    }

    /**
     * Get default schema grammar instance
     *
     * @return \NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultGrammar()
    {
        return new MySqlGrammar;
    }
}