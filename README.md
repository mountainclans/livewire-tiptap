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

_Обратите внимание, что для корректной стилизации в вашем проекте должен использоваться TailwindCSS._

Добавьте в `tailwind.config.js` в секцию `content`:

```js
'./vendor/mountainclans/livewire-tiptap/resources/views/**/*.blade.php'
```

---
### Если редактор используется для заливки изображений:

Опубликуйте и примените миграцию:

```bash
php artisan vendor:publish --tag="livewire-tiptap-migrations"
php artisan migrate
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

Используйте атрибут `translatable`, если Вы хотите использовать компонент вместе с [<x-translatable>](https://github.com/mountainclans/livewire-translatable).

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
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
