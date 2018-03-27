<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Test;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Blueprint;
use NtimYeboah\LaravelDatabaseTrigger\Schema\QueryStatement;
use NtimYeboah\LaravelDatabaseTrigger\Schema\Grammars\MySqlGrammar;

class MySqlGrammarTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCreateTriggerToRunInsertStatement()
    {
        $trigger = 'before_employees_update';
        $eventTable = 'employees';
        $callback = function (QueryStatement $query) {
            $query->insert('employees_audit', function() {
                return 'first_name=old.first_name,last_name=old.last_name';
            });
        };
        
        $blueprint = new Blueprint($trigger, $eventTable, $callback);
        $blueprint->create()->before()->update();

        $conn = $this->getConnection();
        $statements = $blueprint->toSql($conn, $this->getGrammar());
        
        $createClause = 'delimiter $$ create trigger before_employees_update before update on employees for each row begin insert into employees_audit set first_name=old.first_name,last_name=old.last_name end$$ delimiter ;';

        $this->assertEquals($createClause, $statements[0]);
    }

    public function testCreateTriggerToRunUpdateStatement()
    {
        $trigger = 'after_employees_insert';
        $eventTable = 'employees';
        $callback = function (QueryStatement $query) {
            $query->update('employees_audit', function() {
                return 'first_name=new.first_name,last_name=new.last_name';
            });
        };

        $blueprint = new Blueprint($trigger, $eventTable, $callback);
        $blueprint->create()->after()->insert();

        $conn = $this->getConnection();
        $statements = $blueprint->toSql($conn, $this->getGrammar());
        
        $createClause = 'delimiter $$ create trigger after_employees_insert after insert on employees for each row begin update employees_audit set first_name=new.first_name,last_name=new.last_name end$$ delimiter ;';

        $this->assertEquals($createClause, $statements[0]);
    }

    public function testCreateTriggerToRunArbitraryStatement()
    {
        $trigger = 'after_orders_delete';
        $eventTable = 'orders';
        $callback = function (QueryStatement $query) {
            $query->statement(function() {
                return 'delete from orders where id = 1';
            });
        };

        $blueprint = new Blueprint($trigger, $eventTable, $callback);
        $blueprint->create()->after()->delete();

        $conn = $this->getConnection();
        $statements = $blueprint->toSql($conn, $this->getGrammar());
        
        $createClause = 'delimiter $$ create trigger after_orders_delete after delete on orders for each row begin delete from orders where id = 1; end$$ delimiter ;';

        $this->assertEquals($createClause, $statements[0]);
    }

    public function testDropTrigger()
    {
        $trigger = 'before_employees_update';
        $blueprint = new Blueprint($trigger);
        $blueprint->dropIfExists('before_employees_update');

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