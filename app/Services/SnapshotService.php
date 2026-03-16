<?php

namespace App\Services;

use App\Models\AnalysisSnapshot;
use App\Models\Company;

class SnapshotService
{
    public function prendreSnapshot(Company $company): void
    {
        $dernier = $company->snapshots()->latest('prise_le')->first();

        if ($dernier && $dernier->prise_le->diffInDays(now()) < 7) {
            return;
        }

        AnalysisSnapshot::create([
            'company_id'      => $company->id,
            'score_digital'   => $company->score_digital,
            'score_croissance' => $company->score_croissance,
            'presence_web'    => $company->presence_web,
            'prise_le'        => now(),
        ]);
    }

    public function calculerProgression(Company $company): array
    {
        $snapshots = $company->snapshots()->orderBy('prise_le')->get();

        if ($snapshots->count() < 2) {
            return ['evolution_digital' => 0, 'evolution_croissance' => 0, 'snapshots' => $snapshots];
        }

        $premier = $snapshots->first();
        $dernier = $snapshots->last();

        return [
            'evolution_digital'    => $dernier->score_digital - $premier->score_digital,
            'evolution_croissance' => $dernier->score_croissance - $premier->score_croissance,
            'snapshots'            => $snapshots,
        ];
    }
}
