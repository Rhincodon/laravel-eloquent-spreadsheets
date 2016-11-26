# Laravel Eloquent sync with Google Spreadsheets

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rhincodon/laravel-eloquent-spreadsheets.svg?style=flat-square)](https://packagist.org/packages/rhincodon/laravel-eloquent-spreadsheets)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![StyleCI](https://styleci.io/repos/74288997/shield)](https://styleci.io/repos/74288997)

Fast Preview:
![Preview](https://cloud.githubusercontent.com/assets/6630959/20640355/ca3f0676-b3ec-11e6-9d61-229092be3c09.gif "Preview")

## Installation

Via Composer:

``` bash
$ composer require rhincodon/laravel-eloquent-spreadsheets
```

Register Service Provider in `config/app.php`:

```php
Rhinodontypicus\EloquentSpreadsheets\EloquentSpreadsheetsServiceProvider::class,
```

Publish config:

```bash
php artisan vendor:publish --provider="Rhinodontypicus\EloquentSpreadsheets\EloquentSpreadsheetsServiceProvider" --tag="config"
```

### Fetch Google Credentials

In Google Console create Service Account:

Step 1:
![Step 1](https://cloud.githubusercontent.com/assets/6630959/20640446/1ad7ab2c-b3ef-11e6-8320-5c2d521c88f2.jpg)

Step 2:
![Step 2](https://cloud.githubusercontent.com/assets/6630959/20640447/1ad94fc2-b3ef-11e6-93fe-bac58580d77f.jpg)

After pressing Create it will give you credentials file, which you can use in your project. Just save it somewhere in storage.

You also need to give access to spreadsheets that will be used to that service Account. Just copy `client_email` from credentials file, and use it to give access.

## Usage

To start use package you need to specify array of models that will be synced in config file. It is a self-explainable config.

For now is working add, update, delete actions from app to spreadsheet. And update action from spreadsheet to app. All actions processed in queue.

If you want sync(only update action work) data back to app from spreadsheet, schedule following command:

```php
$schedule->command('eloquent-spreadsheets:sync')->hourly();
```

## Roadmap

- [ ] Tests

## Credits

- [rhinodontypicus](https://github.com/Rhincodon)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
