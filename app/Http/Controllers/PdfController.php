<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\PdfService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function telecharger(Request $request, string $slug, PdfService $pdfService)
    {
        $company = Company::where('slug', $slug)
            ->where('user_id', $request->user()->id)
            ->with('analyses')
            ->firstOrFail();

        // Vérifier l'accès PDF selon le plan
        if (!$request->user()->aAcces('pdf_export')) {
            return redirect()->route('subscription.upgrade')
                ->with('message', 'Le téléchargement PDF nécessite le plan Starter ou supérieur.');
        }

        $nomFichier = $pdfService->genererRapport($company);

        return response()->download(
            storage_path("app/public/rapports/{$nomFichier}"),
            "rapport-{$company->nom}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
