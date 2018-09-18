<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Test;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar;

class MySqlGrammarTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCanCreateTrigger()
    {
        $trigger = 'after_users_delete';
        $eventObjectTable = 'users';
        $statement = function () {
            return 'DELETE FROM users WHERE id = 1;';
        };

        $blueprint = new Blueprint($trigger);
        $blueprint->create()
            ->on($eventObjectTable)
            ->statement($statement)
            ->after()
            ->delete();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar());

        $actionStatement = 'create trigger after_users_delete after delete on `users` for each row begin DELETE FROM users WHERE id = 1; end';

        $this->assertEquals($actionStatement, $statements[0]);
    }

    public function testDropTrigger()
    {
        $trigger = 'before_employees_update';
        $blueprint = new Blueprint($trigger);
        $blueprint->dropIfExists($trigger);

        $conn = $this->getConnection();
        $statement = $blueprint->toSql($conn, $this->getGrammar());

        $dropClause = 'drop trigger if exists before_employees_update';

        $this->assertEquals($dropClause, $statement[0]);
    }

    private function getConnection()
    {
        return m::mock('Illuminate\Database\Connection');
    }

    private function getGrammar()
    {
        return new MySqlGrammar;
    }
}
