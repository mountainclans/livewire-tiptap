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
