<?php

namespace NtimYeboah\LaravelDatabaseTrigger;

use Illuminate\Support\ServiceProvider;
use NtimYeboah\LaravelDatabaseTrigger\Schema\MySqlBuilder;

class TriggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MySqlBuilder::class, function() {
            return new MySqlBuilder(app('db.connection'));
        });

        $this->app->alias(MySqlBuilder::class, 'trigger-builder');
    }
}