<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

use Closure;
use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Event;
use NtimYeboah\LaravelDatabaseTrigger\Schema\ActionTime;
use NtimYeboah\LaravelDatabaseTrigger\Schema\QueryStatement;

class Blueprint
{
    /**
     * The trigger the blueprint describes.
     *
     * @var string
     */
    public $trigger;

    /**
     * Table event happens on.
     *
     * @var string
     */
    public $eventTable;

    /**
     * Table to perform action.
     *
     * @var string
     */
    public $table;

    /**
     * The operation to perform on a table.
     *
     * @var string
     */
    public $tableOperation;

    /**
     * Table event time.
     *
     * @var string
     */
    public $time;

    /**
     * Table event.
     *
     * @var string
     */
    public $event;

    /**
     * Statement to run. 
     *
     * @var string|array
     */
    public $clause;

    /**
     * The commands that should be run for the trigger.
     *
     * @var Illuminate\Support\Fluent[]
     */
    public $commands;

    /**
     * Create a new schema blueprint.
     *
     * @param string $trigger
     * @param string $eventTable
     * @param Closure $callable
     * 
     * @return void
     */
    public function __construct($trigger, $eventTable = null, Closure $callback = null)
    {
        $this->trigger = $trigger;
        $this->eventTable = $eventTable;

        if (! is_null($callback)) {
            $callback($queryStatement = $this->getQueryStatement());
            
            $this->table = $queryStatement->table;
            $this->tableOperation = $queryStatement->operation;
            $this->clause = $queryStatement->clause;
        }    
    }

    /**
     * Execute the blueprint against the database.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  \Illuminate\Database\Schema\Grammars\Grammar $grammar
     * 
     * @return void
     */
    public function build(Connection $connection, Grammar $grammar)
    {
        foreach ($this->toSql($connection, $grammar) as $statement) {
            $connection->statement($statement);
        }
    }

    /**
     * Get the raw SQL statements for the blueprint.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  \Illuminate\Database\Schema\Grammars\Grammar $grammar
     * 
     * @return array
     */
    public function toSql(Connection $connection, Grammar $grammar)
    {
        $statements = [];
        
        foreach ($this->commands as $command) {
            $method = 'compile'.ucfirst($command->name);
            
            if (method_exists($grammar, $method)) {
                if (! is_null($sql = $grammar->$method($this, $command, $connection))) {
                    $statements = array_merge($statements, (array) $sql);
                }
            }
        }
        
        return $statements;
    }

    /**
     * Indicate that the trigger should be dropped.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function dropIfExists()
    {
        return $this->addCommand('dropIfExists');
    }

     /**
     * Indicate that the trigger needs to be created.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function create()
    {
        $this->addCommand('create');

        return $this;
    }

    /**
     * Trigger after.
     *
     * @return NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint
     */
    public function after()
    {
        $this->time = ActionTime::after();

        return $this;
    }

    /**
     * Trigger before.
     *
     * @return  NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint
     */
    public function before()
    {
        $this->time = ActionTime::before();

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
    }

    /**
     * Trigger update event
     * 
     * @return void
     */
    public function update()
    {        
        $this->event = Event::update();
    }

    /**
     * Trigger delete event
     * 
     * @return void
     */
    public function delete()
    {        
        $this->event = Event::delete();
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param  string  $name
     * @param  array  $parameters
     * 
     * @return \Illuminate\Support\Fluent
     */
    protected function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param  string  $name
     * @param  array  $parameters
     * 
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

    /**
     * Get the commands on the blueprint.
     *
     * @return \Illuminate\Support\Fluent[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Get query statement.
     *
     * @return \NtimYeboah\LaravelDatabaseTriggers\Schema\QueryStatement
     */
    public function getQueryStatement()
    {
        return new QueryStatement;
    }
}