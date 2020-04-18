<?php

namespace NtimYeboah\LaravelDatabaseTrigger;

use Illuminate\Support\ServiceProvider;
use NtimYeboah\LaravelDatabaseTrigger\Command\TriggerMakeCommand;
use NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder;

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
