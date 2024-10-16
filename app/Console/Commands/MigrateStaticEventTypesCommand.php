<?php

namespace App\Console\Commands;

use App\Models\EventType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateStaticEventTypesCommand extends Command
{
    
    protected $signature = 'migrate:static-event-types';

    
    protected $description = 'Migrate old static event types';

    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function handle()
    {
        $this->copyFilesToNewDirectory();

                if ($this->hasBeenRun()) {
            $this->info('This command was run before');
            return;
        }

        app(\EventTypesSeeder::class)->run();
    }

    protected function hasBeenRun()
    {
        try {
            return EventType::count() > 1;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->warn('Have you run the migrations?');
        }
    }

    protected function copyFilesToNewDirectory()
    {
        $iconPath = base_path('resources/assets/img/icons');

                foreach (new \DirectoryIterator($iconPath) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            Storage::disk('public')->put($fileInfo->getFilename(), file_get_contents($fileInfo->getPathname()));
        }
    }


}
