<?php

namespace MountainClans\LivewireTiptap;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MountainClans\LivewireTiptap\Commands\LivewireTiptapCommand;

class LivewireTiptapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('livewire-tiptap')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_livewire_tiptap_table')
            ->hasCommand(LivewireTiptapCommand::class);
    }
}
