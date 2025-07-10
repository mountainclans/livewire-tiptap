<?php

namespace MountainClans\LivewireTiptap;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireTiptapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-tiptap')
            ->hasViews()
            ->hasMigration('create_editor_media_table');
    }

    public function packageBooted(): void
    {
        Blade::component('livewire-tiptap::components/tiptap-button', 'ui.tiptap-button');
        Blade::component('livewire-tiptap::components/tiptap', 'ui.tiptap');
    }
}
