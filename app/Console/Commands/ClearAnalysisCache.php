<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearAnalysisCache extends Command
{
    protected $signature   = 'analysis:clear-cache';
    protected $description = 'Vide le cache des analyses d\'entreprise';

    public function handle(): void
    {
        Cache::flush();
        $this->info('Cache vidé.');
    }
}
