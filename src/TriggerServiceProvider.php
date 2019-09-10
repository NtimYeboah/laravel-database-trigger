<?php

namespace DariusIII\LaravelDatabaseTrigger;

use Illuminate\Support\ServiceProvider;
use DariusIII\LaravelDatabaseTrigger\Schema\MySqlBuilder;
use DariusIII\LaravelDatabaseTrigger\Command\TriggerMakeCommand;

class TriggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TriggerMakeCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->singleton('trigger-builder', function () {
            return new MySqlBuilder(app('db.connection'));
        });
    }
}
