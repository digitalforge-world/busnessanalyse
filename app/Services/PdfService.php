<?php

namespace App\Services;

use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function genererRapport(Company $company): string
    {
        $analyse = $company->derniereAnalyse();

        $pdf = Pdf::loadView('pdf.rapport', [
            'company'  => $company,
            'analyse'  => $analyse,
            'date'     => now()->format('d/m/Y'),
        ])
        ->setPaper('A4', 'portrait')
        ->setOptions([
            'defaultFont'  => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 150,
        ]);

        $nomFichier = 'rapport-' . $company->slug . '-' . now()->format('Ymd') . '.pdf';
        $chemin     = storage_path("app/public/rapports/{$nomFichier}");

        if (!file_exists(dirname($chemin))) {
            mkdir(dirname($chemin), 0755, true);
        }

        $pdf->save($chemin);

        return $nomFichier;
    }
}
