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
