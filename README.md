# Tiptap editor for Livewire

## Установка

Установите пакет при помощи Composer:

```bash
composer require mountainclans/livewire-tiptap
```

Поскольку пакет основан на [Tiptap Editor](), установите его командой

```bash
npm install @tiptap/core @tiptap/pm @tiptap/starter-kit  @tiptap/extension-table@^2.0.0 @tiptap/extension-table-row@^2.0.0 @tiptap/extension-table-header@^2.0.0 @tiptap/extension-table-cell@^2.0.0
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

---
Опционально, Вы можете опубликовать `views` для их переопределения:

```bash
php artisan vendor:publish --tag="livewire-tiptap-views"
```

## Использование

```bladehtml
<x-ui.tiptap wire:model="content"
             :with-image="true"
             :with-table="true"
             translatable
             height="700"
             placeholder="{{ __('Content') }}"
             label="{{ __('Page`s content *') }}"
/>
```

Используйте атрибут `translatable`, если Вы хотите использовать компонент как [translatable поле](https://github.com/mountainclans/livewire-translatable).

### Разрешённые инструменты

По умолчанию доступна вся панель. Атрибут `tools` ограничивает её явным списком:

```bladehtml
<x-ui.tiptap wire:model="description"
             :tools="['bold', 'bullet_list', 'ordered_list']"
             label="{{ __('Description') }}"
/>
```

Набор управляет не только панелью, но и расширениями редактора: спрятанная
кнопка сама по себе не мешает ни горячей клавише, ни вставке из буфера,
поэтому неразрешённое форматирование выбрасывается схемой при вводе и вставке.

Ключи: `bold`, `italic`, `underline`, `strike`, `link`, `bullet_list`,
`ordered_list`, `blockquote`, `headings`, `text_size`, `align`, `image`, `table`.

Без `tools` поведение прежнее — включено всё, поэтому старые поля при открытии
ничего не теряют. Если `tools` задан, `image` и `table` показываются только
когда есть и соответствующий ключ, и `with-image` / `with-table`.

### Исходный код

Атрибут `with-html` добавляет в панель кнопку «HTML». Она заменяет редактор полем
с исходным кодом: его можно прочитать и поправить руками.

```bladehtml
<x-ui.tiptap wire:model="description"
             with-html
             label="{{ __('Description') }}"
/>
```

- Код показан так, как он лежит в поле: шорткоды — текстом `[code]`. Для чтения
  между блоками расставлены переносы строк.
- Правки попадают в поле сразу, по мере набора.
- При возврате в редактор текст проходит через его схему: разметка, которой нет
  в наборе `tools`, отбрасывается, и поле получает то, что видно в редакторе.
  Если сохранить форму, не выходя из режима кода, текст сохранится как набран.
- Просмотр кода без правок значение поля не меняет.

### Шорткоды

Шорткод — служебная метка вида `[code]`, которую разбирает сайт, а не редактор:
место для блока, разрыв «под кат», особый перенос. Пакет не знает ни одного кода
заранее — набор задаёт приложение атрибутом `shortcodes`:

```bladehtml
<x-ui.tiptap wire:model="description"
             :shortcodes="[
                 ['code' => 'CUT', 'label' => 'Под кат', 'kind' => 'block', 'hint' => 'Всё ниже свёрнуто под кнопкой'],
                 ['code' => 'br', 'label' => 'Перенос'],
             ]"
             label="{{ __('Description') }}"
/>
```

| Ключ    | Значение                                                                                   |
|---------|--------------------------------------------------------------------------------------------|
| `code`  | Текст между скобками: латиница, цифры, `_` и `-`. Регистр учитывается.                       |
| `label` | Подпись на кнопке панели и на плашке рядом с меткой. По умолчанию сама метка.                |
| `kind`  | `block` — метка занимает отдельную строку между блоками; `inline` (по умолчанию) — в строке. |
| `hint`  | Подсказка у кнопки. По умолчанию подпись.                                                   |

В сохранённом HTML метка остаётся текстом `[code]`, поэтому код сайта, который её
разбирает, менять не нужно, а давние тексты с метками открываются как есть.
В редакторе метка показана неделимой плашкой, на которой написаны сам код и
подпись — `[CUT] — Под кат`: её нельзя испортить набором или оформлением. Блочная метка не может оказаться посреди абзаца — при открытии и при
вставке абзац делится на два, а в сохранённом HTML она стоит между блоками без
обёртки: `<p>…</p>[CUT]<p>…</p>`.

Метка, набранная руками, превращается в плашку сразу. Коды, которых нет в наборе,
остаются обычным текстом. Набор не зависит от `tools`.

Неверное определение (недопустимый код, незнакомый `kind`, повтор кода) —
исключение `InvalidArgumentException` при выводе компонента.

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
