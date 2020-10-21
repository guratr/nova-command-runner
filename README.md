# Laravel Nova tool for running Artisan commands.
[![Latest Version on Github](https://img.shields.io/packagist/v/guratr/nova-command-runner.svg?style=flat)](https://packagist.org/packages/guratr/nova-command-runner)
[![Total Downloads](https://img.shields.io/packagist/dt/guratr/nova-command-runner.svg?style=flat)](https://packagist.org/packages/guratr/nova-command-runner)

This [Nova](https://nova.laravel.com) tool lets you:
- run & queue artisan commands
- specify options for commands
- get command result
- view commands history

![screenshot of the command runner tool](https://user-images.githubusercontent.com/1502853/50797697-16c4f100-12ef-11e9-99b0-2bf9736236f1.png)

## Installation

You can install the nova tool in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require guratr/nova-command-runner
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvder.php

// ...

public function tools()
{
    return [
        // ...
        new \Guratr\CommandRunner\CommandRunner,
    ];
}
```

Publish the config file:

``` bash
php artisan vendor:publish --provider="Guratr\CommandRunner\ToolServiceProvider"
```

Add your commands to config/nova-command-runner.php

Available options:
- run : command to run (e.g. `route:cache`)
- options : array of options for command (e.g. `['--allow' => ['127.0.0.1']]`)  
- queue : boolean (will use default settings when true) or array (e.g. `['connection' => 'database', 'queue' => 'default']`)
- type : button class (primary, secondary, success, danger, warning, info, light, dark, link) 
- group: Group name (optional)

## Usage

Click on the "Command Runner" menu item in your Nova app to see the tool.
