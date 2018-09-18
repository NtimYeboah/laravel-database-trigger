<?php

namespace NtimYeboah\LaravelDatabaseTrigger\Command;

use Illuminate\Support\Str;
use Illuminate\Support\Composer;
use Illuminate\Database\Console\Migrations\BaseCommand;
use NtimYeboah\LaravelDatabaseTrigger\Migrations\MigrationCreator;

class TriggerMakeCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trigger {name : The name of the trigger}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trigger migration file';

    /**
     * Migration creator instance.
     *
     * @var \NtimYeboah\LaravelDatabaseTriggers\Migrations\MigrationCreator;
     */
    protected $creator;

    /**
     * The composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a trigger migration instance.
     *
     * @param MigrationCreator $creator
     * @param Composer $composer
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    public function handle()
    {
        $name = Str::snake(trim($this->input->getArgument('name')));

        $eventObjectTable = $this->ask('Event object table name');

        $actionTiming = $this->choice('Action timing', ['after', 'before']);
        $this->info("Action timing: {$actionTiming}");

        $event = $this->choice('Event manipulation', ['insert', 'update', 'delete']);
        $this->info("Event manipulation: {$event} \n");

        $this->writeMigration($name, $eventObjectTable, $actionTiming, $event);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write to migration file.
     *
     * @param string $name
     * @param string $eventObjectTable
     * @param string $actionTiming
     * @param string $event
     * @return void
     */
    private function writeMigration($name, $eventObjectTable, $actionTiming, $event)
    {
        $file = pathinfo($this->creator->write(
            $name,
            $eventObjectTable,
            $actionTiming,
            $event,
            $this->getMigrationPath()
        ), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> {$file}");
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                            ? $this->laravel->basePath().'/'.$targetPath
                            : $targetPath;
        }

        return parent::getMigrationPath();
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     *
     * @return bool
     */
    protected function usingRealPath()
    {
        return $this->input->hasOption('realpath') && $this->option('realpath');
    }
}
