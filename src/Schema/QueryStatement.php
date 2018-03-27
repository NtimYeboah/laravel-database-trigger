<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Schema;

use Closure;

class QueryStatement
{
    /**
     * Table to run trigger statements.
     *
     * @var string
     */
    public $table;

    /**
     * Trigger table operation.
     *
     * @var string
     */
    public $operation;

    /**
     * Trigger clause to run.
     *
     * @var string|array
     */
    public $clause;

    /**
     * Insert databse operation.
     *
     * @param string $table
     * @param Closure $callback
     * 
     * @return void
     */
    public function insert($table, Closure $callback)
    {
        $this->operation = 'insert';

        $this->table = $table;
        $this->clause = $callback();
    }

    /**
     * Update database operation
     * 
     * @param string $table
     * @param Closure $callback
     * 
     * @return void
     */
    public function update($table, Closure $callback)
    {
        $this->operation = 'update';

        $this->table = $table;
        $this->clause = $callback();
    }

    /**
     * Run arbitrary sql statments for trigger.
     *
     * @param string $clause
     * 
     * @return void
     */
    public function statement(Closure $callback) 
    {
        $this->clause = $callback();
    }  
}