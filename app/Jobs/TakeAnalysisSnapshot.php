<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\SnapshotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TakeAnalysisSnapshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Company $company,
    ) {}

    public function handle(SnapshotService $snapshotService): void
    {
        $snapshotService->prendreSnapshot($this->company);
    }
}
