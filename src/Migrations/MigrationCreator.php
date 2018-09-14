<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Migrations;

use Closure;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator
{
    /**
     * The filesystem instance
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The registered post create hooks
     *
     * @var array
     */
    protected $postCreate = [];

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Create a new migration at the given path.
     *
     * @param string $name
     * @param string $eventObjectTable
     * @param string $actionTiming
     * @param string $event
     * @param string|null $path
     * 
     * @return void
     */
    public function create($name, $eventObjectTable, $actionTiming, $event, $path)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        $stub = $this->files->get($this->stubPath().'/create.stub');
        
        $this->files->put(
            $path = $this->getPath($name, $path),
            $this->populateStub($name, $eventObjectTable, $actionTiming, $event, $stub)
        );

        $this->firePostCreateHooks();

        return $path;
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param  string $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist($name)
    {
        if (class_exists($className = $this->getClassName($name))) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }
    }

    /**
     * Populate migration stub
     * 
     * @param string $name
     * @param string $eventObjectTable
     * @param string $actionTiming
     * @param string $event
     * @param string $stub
     * 
     * @return string;
     */
    protected function populateStub($name, $eventObjectTable, $actionTiming, $event, $stub)
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
     * Fire the registered post create hooks.
     *
     * @return void
     */
    protected function firePostCreateHooks()
    {
        foreach ($this->postCreate as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Register a post migration create hook.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function afterCreate(Closure $callback)
    {
        $this->postCreate[] = $callback;
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

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * 
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_create_'.$name.'_trigger.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }
}