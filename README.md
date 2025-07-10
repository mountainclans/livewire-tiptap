# Tiptap editor for Livewire

## Установка

Установите пакет при помощи Composer:

```bash
composer require mountainclans/livewire-tiptap
```

Опубликуйте и примените миграцию:

```bash
php artisan vendor:publish --tag="livewire-tiptap-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="livewire-tiptap-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="livewire-tiptap-views"
```

## Usage

```php
$livewireTiptap = new MountainClans\LivewireTiptap();
echo $livewireTiptap->echoPhrase('Hello, MountainClans!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Vladimir Bajenov](https://github.com/mountainclans)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
