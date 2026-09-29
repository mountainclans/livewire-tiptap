<?php

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use MountainClans\LivewireTiptap\LivewireTiptapServiceProvider;

it('boots the service provider', function () {
    expect(app()->getLoadedProviders())
        ->toHaveKey(LivewireTiptapServiceProvider::class);
});

it('registers the tiptap blade component aliases', function () {
    $aliases = app('blade.compiler')->getClassComponentAliases();

    expect($aliases)
        ->toHaveKey('ui.tiptap')
        ->toHaveKey('ui.tiptap-button');
});

it('registers the image upload route', function () {
    expect(Route::has('tiptap.upload-image'))->toBeTrue();
});

it('excludes CSRF middleware from the upload route', function () {
    $route = app('router')->getRoutes()->getByName('tiptap.upload-image');

    // На L11+ web-группа регистрирует CSRF как PreventRequestForgery,
    // поэтому исключать нужно именно его (а не deprecated VerifyCsrfToken).
    expect($route->excludedMiddleware())
        ->toContain(PreventRequestForgery::class);
});

it('renders the x-ui.tiptap component', function () {
    $html = Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" />'
    );

    expect($html)
        ->toContain('Content')
        ->toContain('tiptap-');
});

it('renders the whole toolbar and passes no tools argument when tools are not given', function () {
    $html = Blade::render('<x-ui.tiptap label="Content" wire:model="content" />');

    expect($html)
        ->toContain('tiptap($wire.entangle(\'content\'))')
        ->toContain('toggleBold()')
        ->toContain('toggleItalic()')
        ->toContain('toggleBulletList()')
        ->toContain('setTextAlignment');
});

it('keeps only the allowed tools in the toolbar and hands the list to the editor', function () {
    $html = Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" :tools="[\'bold\', \'bullet_list\']" />'
    );

    // Набор уезжает в редактор: он гасит расширения, а не только кнопки
    expect(html_entity_decode($html))
        ->toContain('tiptap($wire.entangle(\'content\'), ["bold","bullet_list"])')
        ->toContain('toggleBold()')
        ->toContain('toggleBulletList()')
        ->not->toContain('toggleItalic()')
        ->not->toContain('toggleOrderedList()')
        ->not->toContain('setTextAlignment');
});

it('hides the image button when the tools list omits it', function () {
    $withImage = Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" :with-image="true" :tools="[\'bold\', \'image\']" />'
    );
    $withoutImage = Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" :with-image="true" :tools="[\'bold\']" />'
    );

    expect($withImage)->toContain('addImage()')
        ->and($withoutImage)->not->toContain('addImage()');
});

it('has no source mode unless asked for', function () {
    $html = Blade::render('<x-ui.tiptap label="Content" wire:model="content" />');

    expect($html)
        ->not->toContain('toggleSource()')
        ->not->toContain('<textarea')
        ->not->toContain('sourceMode');
});

it('adds the source mode button and field with the with-html attribute', function () {
    $html = Blade::render('<x-ui.tiptap label="Content" wire:model="content" with-html height="300" />');

    expect($html)
        ->toContain('toggleSource()')
        ->toContain('x-model="source"')
        ->toContain('x-show="!sourceMode"')
        ->toContain('height: 300px;')
        // Аргументы редактора от режима кода не зависят
        ->toContain('tiptap($wire.entangle(\'content\'))');
});

it('turns into a single-line editor: no formatting, plain text storage', function () {
    $html = html_entity_decode(Blade::render(
        '<x-ui.tiptap label="Title" wire:model="title" single-line :tools="[\'bold\']" :shortcodes="[[\'code\' => \'hl\', \'kind\' => \'pair\']]" />'
    ), ENT_QUOTES);

    expect($html)
        // Набор инструментов в одной строке пуст, какой бы ни передали
        ->toContain('tiptap($wire.entangle(\'title\'), [], [{"code":"hl"')
        ->toContain('{"singleLine":true})')
        ->toContain("insertShortcode('hl')")
        ->not->toContain('toggleBold()')
        ->not->toContain('toggleItalic()');
});

it('hides the toolbar of a single-line editor that has nothing to show in it', function () {
    $bare = Blade::render('<x-ui.tiptap label="Title" wire:model="title" single-line />');
    $withSource = Blade::render('<x-ui.tiptap label="Title" wire:model="title" single-line with-html />');

    expect($bare)->toContain('shrink-0 px-1.5 py-1.5 hidden')
        ->and($withSource)->not->toContain('py-1.5 hidden')
        ->and($withSource)->toContain('toggleSource()');
});
