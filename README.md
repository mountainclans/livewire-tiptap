# Tiptap editor for Livewire

## Установка

Установите пакет при помощи Composer:

```bash
composer require mountainclans/livewire-tiptap
```

Поскольку пакет основан на [Tiptap Editor](), установите его командой

```bash
npm install @tiptap/core @tiptap/pm @tiptap/starter-kit
```

Опубликуйте и примените миграцию:

```bash
php artisan vendor:publish --tag="livewire-tiptap-migrations"
php artisan migrate
```

Добавьте в `app.js` следующие строки:

```js
import tiptap from '../../vendor/mountainclans/livewire-tiptap/resources/js/tiptap';
Alpine.data('tiptap', tiptap);
```

---
Опционально, Вы можете опубликовать `views` для их переопределения:

```bash
php artisan vendor:publish --tag="livewire-tiptap-views"
```

## Использование

## Авторы

- [Vladimir Bajenov](https://github.com/mountainclans)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
