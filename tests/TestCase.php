<?php

namespace MountainClans\LivewireTiptap\Tests;

use Illuminate\Support\ViewErrorBag;
use Livewire\LivewireServiceProvider;
use MountainClans\LivewireTiptap\LivewireTiptapServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Вне HTTP-запроса middleware ShareErrorsFromSession не отрабатывает,
        // а компонент использует @error — расшариваем пустой bag вручную.
        $this->app['view']->share('errors', new ViewErrorBag);
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireTiptapServiceProvider::class,
        ];
    }
}
