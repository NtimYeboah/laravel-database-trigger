<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Migrations;

use Illuminate\Support\Str;
use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;

class MigrationCreator extends BaseMigrationCreator
{
    /**
     * Create a new migration at the given path.
     *
     * @param string $name
     * @param string $eventObjectTable
     * @param string $actionTiming
     * @param string $event
     * @param string|null $path
     * @return string
     */
    public function write($name, $eventObjectTable, $actionTiming, $event, $path)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        $stub = $this->files->get($this->stubPath().'/create.stub');

        $this->files->put(
            $path = $this->getPath($name, $path),
            $this->populate($name, $eventObjectTable, $actionTiming, $event, $stub)
        );

        return $path;
    }

    /**
     * Populate migration stub.
     *
     * @param string $name
     * @param string $eventObjectTable
     * @param string $actionTiming
     * @param string $event
     * @param string $stub
     * @return string;
     */
    protected function populate($name, $eventObjectTable, $actionTiming, $event, $stub)
    {
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);
        $stub = str_replace('DummyName', $name, $stub);
        $stub = str_replace('DummyEventObjectTable', $eventObjectTable, $stub);
        $stub = str_replace('DummyActionTiming', $actionTiming, $stub);
        $stub = str_replace('DummyEvent', $event, $stub);

        return $stub;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string $name
     * @return string
     */
    protected function getClassName($name)
    {
        $studlyName = Str::studly($name);

        return "Create{$studlyName}Trigger";
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_create_'.$name.'_trigger.php';
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
}
