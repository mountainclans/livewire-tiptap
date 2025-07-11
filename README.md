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

Добавьте в `app.js` следующие строки:

```js
import tiptap from '../../vendor/mountainclans/livewire-tiptap/resources/js/tiptap';
Alpine.data('tiptap', tiptap);
```

Добавьте в `app.css` следующие строки:

```css
@import '../../vendor/mountainclans/livewire-tiptap/resources/css/tiptap.css';
```

_Обратите внимание, что для корректной стилизации в вашем проекте должен использоваться TailwindCSS._

Добавьте в `tailwind.config.js` следующие блоки:

```js
export default {
    content: [
        './vendor/mountainclans/livewire-tiptap/resources/views/**/*.blade.php',
    ],
    plugins: [
        require("flowbite/plugin")({
            wysiwyg: true,
        }),
        require("flowbite-typography"),
    ],
    safelist: [
        'max-w-none',
        'text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl', 'text-4xl', 'text-5xl',
        'w-4', 'h-4', 'w-6', 'h-6', "h-9", 'w-fit', 'max-w-full', 'h-auto',
        'block', 'relative', 'absolute', 'flex',
        "w-64", "w-1/2",
        "rounded-l-lg", "rounded-r-lg",
        "bg-gray-200", 'bg-gray-600', 'bg-gray-700', 'bg-gray-900', "bg-opacity-50", "dark:bg-opacity-80",
        "grid-cols-4", "grid-cols-7",
        "leading-6", "leading-9",
        "shadow-lg",
        "lg:format-md",
        'top-1', 'right-1',
        'my-0', 'my-1',
        'hover:bg-gray-400',
        'rounded', 'rounded-lg',
        'text-center', 'text-white', 'text-xs',
        'items-center', 'justify-center',
        'mx-auto',
        'cursor-pointer',
        'border-none', 'select-none',
    ]
}
```
---
### Если редактор используется для заливки изображений:

Опубликуйте и примените миграцию:

```bash
php artisan vendor:publish --tag="livewire-tiptap-migrations"
php artisan migrate
```

Добавьте в секцию `<head>` шаблона мета-тег `csrf-token`, который необходим для корректной работы запросов при загрузке изображений:

```bladehtml
<meta name="csrf-token" content="{{ csrf_token() }}">
```

---
Опционально, Вы можете опубликовать `views` для их переопределения:

```bash
php artisan vendor:publish --tag="livewire-tiptap-views"
```

## Использование

```bladehtml
<x-ui.tiptap wire:model="content"
             translatable
             height="700"
             placeholder="{{ __('Content') }}"
             label="{{ __('Page`s content *') }}"
/>
```

Используйте атрибут `translatable`, если Вы хотите использовать компонент как [translatable поле](https://github.com/mountainclans/livewire-translatable).

### Настройка модели для обработки изображений
Если Вы заливаете картинки в контент текстового редактора, необходимо настроить их обработку в модели.

Используйте трейт:

```php

class YourModel extends Model
{
    use MountainClans\LivewireTiptap\Traits\HasEditorMedia;
}
```

После сохранения модели с новым полем (в примере `content`), вызовите метод
`processUploadedImages`:

```php
public function saveBlog(): void
{
    $this->validateInput();
    $this->blog->setTranslations('content', $this->content);
    // или $this->blog->content = $this->content, если поле не переводимое
    $this->blog->save();
    
    $this->blog->processUploadedImages('content');
}
```

## Авторы

- [Vladimir Bajenov](https://github.com/mountainclans)
- [ueberdosis](https://github.com/ueberdosis/tiptap)
- [Intervention](https://github.com/Intervention/image)
- [Flowbite](https://github.com/themesberg/flowbite)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
