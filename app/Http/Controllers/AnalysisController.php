<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AnalysisService;
use App\Services\CompetitorService;
use App\Services\SnapshotService;
use App\Jobs\SendWhatsAppReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalysisController extends Controller
{
    public function __construct(
        private AnalysisService    $analysisService,
        private CompetitorService  $competitorService,
        private SnapshotService    $snapshotService,
        private \App\Services\ExportService $exportService,
    ) {}


    public function index(Request $request)
    {
        $user      = auth()->user();

        // --- CODE DE TEST : Auto-upgrade si retour Stripe Réussi (Local uniquement) ---
        if (config('app.env') === 'local' && $request->has('success')) {
            // Note: En production, c'est le Webhook qui fait ce travail.
            // On récupère le dernier plan choisi (on pourrait passer l'info en session ou URL)
            // Pour le test, on va mettre 'pro' par défaut ou essayer de le deviner.
            $user->update(['plan' => 'pro']); 
        }
        // ----------------------------------------------------------------------------

        $historique = $user->companies()->with('analyses')->latest()->take(5)->get();

        return view('analysis.index', compact('user', 'historique'));
    }

    // Lance l'analyse via AJAX
    public function analyser(Request $request): JsonResponse
    {
        $request->validate([
            'entreprise' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $user = $request->user();

        try {
            $company = $this->analysisService->analyserEntreprise(
                $request->input('entreprise'),
                $user
            );

            $analyse     = $company->derniereAnalyse();
            $progression = $this->snapshotService->calculerProgression($company);

            return response()->json([
                'success' => true,
                'html'    => view('analysis.partials.resultat', compact('company', 'analyse', 'progression', 'user'))->render(),
                'company_slug' => $company->slug,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse : ' . $e->getMessage(),
            ], 500);
        }
    }

    // Lance l'analyse des concurrents
    public function analyserConcurrents(Request $request, string $slug): JsonResponse
    {
        $user    = $request->user();
        $company = Company::where('slug', '=', $slug)->where('user_id', '=', $user->id)->firstOrFail();

        if (!$user->aAcces('competitors')) {
            return response()->json(['success' => false, 'upgrade' => true], 403);
        }

        $concurrents = $this->competitorService->analyserConcurrents($company);

        return response()->json([
            'success' => true,
            'html'    => view('analysis.partials.concurrents', compact('company', 'concurrents'))->render(),
        ]);
    }

    // Envoyer le rapport sur WhatsApp
    public function envoyerWhatsApp(Request $request, string $slug): JsonResponse
    {
        $request->validate(['numero' => ['required', 'string']]);
        $user    = $request->user();
        $company = Company::where('slug', '=', $slug)->where('user_id', '=', $user->id)->firstOrFail();

        if (!$user->aAcces('whatsapp')) {
            return response()->json(['success' => false, 'upgrade' => true], 403);
        }

        SendWhatsAppReport::dispatch($company, $request->input('numero'));

        return response()->json(['success' => true, 'message' => 'Rapport envoyé sur WhatsApp.']);
    }

    // Page de résultats détaillés
    public function show(Request $request, string $slug)
    {
        $company = Company::where('slug', $slug)
            ->where('user_id', $request->user()->id)
            ->with(['analyses', 'competitors', 'snapshots'])
            ->firstOrFail();

        $analyse     = $company->derniereAnalyse();
        $progression = $this->snapshotService->calculerProgression($company);
        $user        = $request->user();

        return view('analysis.show', compact('company', 'analyse', 'progression', 'user'));
    }

    public function exporterExcel(Request $request, string $slug)
    {
        $user    = $request->user();
        $company = Company::where('slug', '=', $slug)->where('user_id', '=', $user->id)->firstOrFail();
        $analyse = $company->derniereAnalyse();

        if (!$user->aAcces('pro_exports')) {
            return back()->with('error', 'Votre forfait ne permet pas l\'export Excel.');
        }

        return $this->exportService->exportExcel($company, $analyse);
    }
}
