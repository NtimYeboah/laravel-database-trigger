Laravel Database Trigger
========================

Laravel Database Trigger provides a way to add database triggers to laravel migrations just like you would with database tables. 

[![Build Status](https://travis-ci.org/NtimYeboah/laravel-database-trigger.svg?branch=master)](https://travis-ci.org/NtimYeboah/laravel-database-trigger)


## Installation

Laravel Database Trigger requires [PHP](https://php.net) 7.1 or 7.2. This particular version has been tested with Laravel 5.5 - 5.7 only.
The package currently supports MySQL only.

To get the latest version, simply require the package using [Composer](https://getcomposer.org):

```bash
$ composer require NtimYeboah/laravel-database-trigger
```

Once installed, if you are not using automatic package discovery, then you need to register the `NtimYeboah\LaravelDatabaseTrigger\TriggerServiceProvider` service provider in your `config/app.php`.


## Usage
When the package is installed, `make:trigger` artisan command is added to the list of artisan commands.
To generate a trigger migration file, use the `make:trigger` command. The command requires the name of the trigger and you will be asked
to supply the name of the event object table, action timing and the event that activates the trigger.

```bash
$ php artisan make:trigger after_users_update
```

Possible values for action timing are `after` and `before`.

Possible values for event are `insert`, `update` and `delete`.

For a trigger that uses `after_users_update` as a name, `users` as event object table name, `after` as action timing and `update` as event, the following trigger migration will be generated.

```php
use Illuminate\Database\Migrations\Migration;
use NtimYeboah\LaravelDatabaseTrigger\TriggerFacade as Schema;

class CreateAfterUsersUpdateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('after_users_update')
            ->on('users')
            ->statement(function() {
                return '//...';
            })
            ->after()
            ->update();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users.after_users_update');
    }
}
```

Return the trigger statement from the closure of the `statement` method. 

The following is an example trigger migration to insert into the `users_audit` table after updating a user.

```php

...

/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('after_users_update')
            ->on('users')
            ->statement(function() {
                return 'insert into `users_audit` (`name`, `email`) values (old.name, old.email);';
            })
            ->after()
            ->update();
    }

...

```



## Security

If you discover a security vulnerability within this package, please send an e-mail to Ntim Yeboah at ntimobedyeboah@gmail.com. All security vulnerabilities will be promptly addressed.


## License

Laravel Database Trigger is licensed under [The MIT License (MIT)](LICENSE).