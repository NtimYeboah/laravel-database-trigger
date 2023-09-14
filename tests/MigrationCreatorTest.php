<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Test;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class MigrationCreatorTest extends TestCase
{
    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function trigger_creation_migration_stores_migration_file()
    {
        $creator = $this->getCreator();
        $creator->expects($this->any())->method('getDatePrefix')->will($this->returnValue('foo'));
        $creator->getFilesystem()->shouldReceive('get')->once()->with($creator->stubPath().'/create.stub')
            ->andReturn('DummyClass DummyName DummyEventObjectTable DummyActionTiming DummyEvent');
        $creator->getFilesystem()->shouldReceive('put')->once()->with('corge/foo_create_bar_trigger.php', 'CreateBarTrigger bar baz quz quuz');

        $path = $creator->write('bar', 'baz', 'quz', 'quuz', 'corge');

        $this->assertEquals('corge/foo_create_bar_trigger.php', $path);
    }

    protected function getCreator()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');

        return $this->getMockBuilder('NtimYeboah\LaravelDatabaseTrigger\Migrations\MigrationCreator')
            ->setMethods(['getDatePrefix'])->setConstructorArgs([$files, 'stubs'])->getMock();
    }
}
