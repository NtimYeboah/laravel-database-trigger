Add database trigger to Laravel migrations
==========================================

[![Total Downloads](https://img.shields.io/packagist/dt/ntimyeboah/laravel-database-trigger.svg?style=flat-square)](https://packagist.org/packages/ntimyeboah/laravel-database-trigger)
![Build Status](https://github.com/NtimYeboah/laravel-database-trigger/actions/workflows/tests.yml/badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)


Laravel Database Trigger provides a way to add database trigger to laravel migrations just like you would with database table. 
A trigger is a named database object that is associated with a table, and that activates when a particular event occurs for the table. Read more about triggers [here](https://dev.mysql.com/doc/refman/8.0/en/triggers.html).


## Installation

Laravel Database Trigger requires at least [PHP](https://php.net) 8.2. This particular version supports Laravel v11.*. Check the table below for versions support.

| Package version | Laravel version | PHP version |
|----------|:-------------:|------:|
| 1 | 5.5, 5.6, 5.7 | >= 7.1 |
| 2.* | 5.8 | >= 7.1.3 |
| 3.* | 6.* | >= 7.2.5 or >= 8.0 |
| 4.* | 7.* | >= 7.2.5 or >= 8.0 |
| 5.* | 8.* | >= 7.3 or >= 8.0 |
| 6.* | 9.* | >=8.0 |
| 7.* | 10.* | >=8.1 |
| 8.* | 11.* | >=8.2 |

This package currently supports MySQL only.

To get the latest version, simply require the package using [Composer](https://getcomposer.org):

```bash
composer require ntimyeboah/laravel-database-trigger
```

Once installed, if you are not using automatic package discovery, then you need to register the `NtimYeboah\LaravelDatabaseTrigger\TriggerServiceProvider` service provider in your `config/app.php`.


## Usage
Create a trigger migration file using the `make:trigger` artisan command. 
The command requires the name of the trigger, name of the event object table, action timing and the event that activates the trigger.

```bash
php artisan make:trigger after_users_update
```

### Event object table
The event object table is the name of the table the trigger is associated with.

### Action timing
The activation time for the trigger. Possible values are `after` and `before`. 

`after` - Process action after the change is made on the event object table. 

`before` - Process action prior to the change is made on the event object table.

### Event
The event to activate trigger. A trigger event can be `insert`, `update` and `delete`.

`insert` - Activate trigger when an insert operation is performed on the event object table.

`update` - Activate trigger when an update operation is performed on the event object table.

`delete` - Activate trigger when a delete operation is performed on the event object table.


The following trigger migration file will be generated for a trigger that uses `after_users_update` as a name, `users` as event object table name, `after` as action timing and `update` as event.

```php

use Illuminate\Database\Migrations\Migration;
use NtimYeboah\LaravelDatabaseTrigger\TriggerFacade as Schema;

return new class extends Migration
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
        Schema::dropIfExists('after_users_update');
    }
};

```

Return the trigger statement from the closure of the `statement` method. 

The following is an example trigger migration to insert into the `users_audit` table after updating a user row.

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

## Testing

Run the tests with:

```php
$ composer test
```

## Consider hiring me
I am currently seeking new employment opportunities and would appreciate it if you'd keep me in mind for roles such as Backend Developer.
Kindly contact me at: ntimobedyeboah@gmail.com

This is a link to my CV: [Ntim Yeboah CV](https://docs.google.com/document/d/1jXVsN1NU5AH2XhStxjuwumGIqunoyk0cPPXZr6viaNs/edit?usp=sharing)


## Changelog

Please see [CHANGELOG](https://github.com/NtimYeboah/laravel-database-trigger/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/NtimYeboah/laravel-database-trigger/blob/master/CONTRIBUTING.md) for details.


## Security

If you discover a security vulnerability within this package, please send an e-mail to Ntim Yeboah at ntimobedyeboah@gmail.com. All security vulnerabilities will be promptly addressed.


## License

Laravel Database Trigger is licensed under [The MIT License (MIT)](LICENSE).