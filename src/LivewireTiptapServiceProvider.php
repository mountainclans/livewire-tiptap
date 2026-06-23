<?php

namespace MountainClans\LivewireTiptap;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use MountainClans\LivewireTiptap\Http\Controllers\TiptapImagesController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireTiptapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-tiptap')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_editor_media_table');
    }

    public function packageBooted(): void
    {
        Blade::component('livewire-tiptap::components/tiptap-button', 'ui.tiptap-button');
        Blade::component('livewire-tiptap::components/tiptap', 'ui.tiptap');
    }

    public function packageRegistered(): void
    {
        // На L11+ web-группа регистрирует CSRF как PreventRequestForgery,
        // а VerifyCsrfToken стал его deprecated-подклассом — исключение только
        // по VerifyCsrfToken там не срабатывает. Исключаем оба ради L10–L13;
        // несуществующий на конкретной версии класс роутер просто игнорирует.
        Route::post('/tiptap/upload-image', [TiptapImagesController::class, 'upload'])
            ->name('tiptap.upload-image')
            ->withoutMiddleware([
                PreventRequestForgery::class,
                VerifyCsrfToken::class,
            ]);
    }
}
