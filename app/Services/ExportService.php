<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Analysis;
use App\Exports\AnalysisExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    public function exportExcel(Company $company, Analysis $analysis)
    {
        $filename = 'analyse_' . str_replace(' ', '_', strtolower($company->nom)) . '.xlsx';
        return Excel::download(new AnalysisExport($company, $analysis), $filename);
    }

    public function exportPPT(Company $company, Analysis $analysis)
    {
        // PowerPoint requires PHPPresentation which might not be installed yet.
        // For now, let's focus on Excel as requested.
        return null;
    }
}
