<?php

namespace MountainClans\LivewireTiptap\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MountainClans\LivewireTiptap\LivewireTiptap
 */
class LivewireTiptap extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MountainClans\LivewireTiptap\LivewireTiptap::class;
    }
}
