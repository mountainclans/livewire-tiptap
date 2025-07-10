<?php

namespace MountainClans\LivewireTiptap\Commands;

use Illuminate\Console\Command;

class LivewireTiptapCommand extends Command
{
    public $signature = 'livewire-tiptap';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
