<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Analysis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AnalysisExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        protected Company $company,
        protected Analysis $analysis
    ) {}

    public function title(): string
    {
        return 'Analyse — ' . $this->company->nom;
    }

    public function headings(): array
    {
        return ['Section', 'Détails'];
    }

    public function collection()
    {
        $data = [
            ['Nom Entreprise', $this->company->nom],
            ['Secteur', $this->company->secteur],
            ['Pays', $this->company->pays],
            ['Score Digital', $this->company->score_digital . '%'],
            ['Score Croissance', $this->analysis->score_croissance ?? '—'],
            ['Description', $this->company->description],
            ['', ''],
            ['RECOMMANDATIONS', ''],
        ];

        foreach ($this->analysis->recommandations ?? [] as $reco) {
            $data[] = [$reco['titre'] ?? 'Reco', $reco['description'] ?? ''];
        }

        $data[] = ['', ''];
        $data[] = ['PLAN D\'ACTION COURT TERME', ''];
        foreach ($this->analysis->plan_action['court_terme'] ?? [] as $a) {
            $data[] = ['•', $a];
        }

        if (isset($this->analysis->extra_data['seo_audit'])) {
            $data[] = ['', ''];
            $data[] = ['AUDIT SEO (LIGHTHOUSE)', ''];
            foreach ($this->analysis->extra_data['seo_audit'] as $k => $v) {
                $data[] = [strtoupper($k), $v . '%'];
            }
        }

        return collect($data);
    }
}
