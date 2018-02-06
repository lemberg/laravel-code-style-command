# Laravel Code Style Command

This package adds simple `artisan` command over [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) script.
Validate [PSR2](https://www.php-fig.org/psr/psr-2/) coding standard in you application.

## Requirements

* PHP >= `7.0`
* Laravel = `5.4.*|5.5.*`

## Getting Started

Add the package in your `composer.json`

```
$ composer require --dev lemberg/laravel-code-style-command
```

Add Service Provider (Only for Laravel 5.4)

```
'providers' => [
    Lemberg\LaravelCsc\LaravelCscServiceProvider::class,
],
```

So that's all :)

Check you code style in `app/` directory.

```
$ php artisan code-style
```

Example output:

```
FILE: .../bkhrupa/work/lemberg/laravel-code-style-command/app/User.php
----------------------------------------------------------------------
FOUND 1 ERROR AFFECTING 1 LINE
----------------------------------------------------------------------
 8 | ERROR | [x] Opening brace of a class must be on the line after
   |       |     the definition
----------------------------------------------------------------------
PHPCBF CAN FIX THE 1 MARKED SNIFF VIOLATIONS AUTOMATICALLY
----------------------------------------------------------------------

Time: 116ms; Memory: 6Mb

Finished
```

## Advance usage

```
$ php artisan code-style --help
```

### Config file

Publish `code-style.php` config file.

```
$ php artisan vendor:publish --provider="Lemberg\LaravelCsc\LaravelCscServiceProvider"
```

### Git pre-commit hook

Use git [pre-commit](https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks) hook

Add next code to pre commit hooks file **.git/hooks/pre-commit**.

```
#!/bin/bash

`php ./artisan code-style --printCommand`
```

`pre-commit` hook must be executable

```
$ chmod +x .git/hooks/pre-commit
```

## License

The Apache License. Please see [License File](LICENSE.md) for more information.
