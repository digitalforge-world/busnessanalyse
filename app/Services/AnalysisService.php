<?php

namespace App\Services;

use App\Models\Analysis;
use App\Models\Company;
use App\Models\User;
use App\Services\AI\GeminiService;
use App\Services\AI\GroqService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AnalysisService
{
    public function __construct(
        private GeminiService $gemini,
        private GroqService   $groq,
    ) {}

    public function analyserEntreprise(string $nom, User $user): Company
    {
        $langue   = $user->locale ?? 'fr';
        $cacheKey = 'analysis_' . md5(strtolower(trim($nom)) . $langue);
        $ttl      = config('ai.analysis.cache_ttl', 1440) * 60;

        if (Cache::has($cacheKey)) {
            $user->incrementAnalyses();
            return Cache::get($cacheKey);
        }

        // Étape 1 — Gemini recherche sur le web
        $recherche = $this->gemini->rechercherEntreprise($nom, $langue);

        // Étape 2 — Groq analyse et génère les recommandations
        $analyse = $this->groq->analyserCroissance($recherche, $langue);

        // Étape 3 — Sauvegarde
        $company = $this->sauvegarder($recherche, $analyse, $user);

        $user->incrementAnalyses();
        Cache::put($cacheKey, $company, $ttl);

        return $company;
    }

    private function sauvegarder(array $r, array $a, User $user): Company
    {
        $company = Company::updateOrCreate(
            ['slug' => Str::slug($r['nom'] ?? 'entreprise') . '-' . $user->id],
            [
                'user_id'          => $user->id,
                'nom'              => $r['nom'] ?? 'Inconnu',
                'secteur'          => $r['secteur'] ?? null,
                'pays'             => $r['pays'] ?? null,
                'langue_detectee'  => $r['langue_detectee'] ?? 'fr',
                'description'      => $r['description'] ?? null,
                'annee_fondation'  => $r['annee_fondation'] ?? null,
                'taille'           => $r['taille'] ?? null,
                'score_digital'    => $r['score_digital'] ?? 0,
                'score_croissance' => $a['score_croissance'] ?? 0,
                'presence_web'     => $r['presence_web'] ?? [],
                'points_forts'     => $r['points_forts'] ?? [],
                'points_faibles'   => $r['points_faibles'] ?? [],
                'opportunites'     => $r['opportunites'] ?? [],
            ]
        );

        $company->analyses()->create([
            'type'            => 'full',
            'analyse_ia'      => $a['analyse_ia'] ?? '',
            'recommandations' => $a['recommandations'] ?? [],
            'plan_action'     => $a['plan_action'] ?? [],
            'statut'          => 'done',
            'ia_utilisee'     => 'gemini+groq',
            'tokens_utilises' => $a['_tokens'] ?? 0,
            'langue'          => $user->locale ?? 'fr',
        ]);

        // Prendre un snapshot automatique pour le suivi
        app(SnapshotService::class)->prendreSnapshot($company);

        return $company->load(['analyses', 'competitors', 'snapshots']);
    }
}
