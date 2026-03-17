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
        private GeminiService  $gemini,
        private GroqService    $groq,
        private ScraperService $scraper,
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

        $extraData = [];
        $context   = "";

        // Étape 0 — Recherche web temps réel (ScrapingBee)
        if ($user->aAcces('realtime_search')) {
            $searchResults = $this->scraper->search($nom, $langue);
            if (!empty($searchResults)) {
                $context = "Résultats de recherche Google récents :\n" . json_encode($searchResults);
                $extraData['web_search'] = $searchResults;
            }
        }

        // Étape 1 — Gemini recherche & analyse initiale (enrichie par le contexte web)
        $recherche = $this->gemini->rechercherEntreprise($nom, $langue, $context);
        $urlSite   = $recherche['presence_web']['site_web_url'] ?? null;

        // Étape 2 — Audit SEO & Tech (si URL identifiée)
        if ($urlSite) {
            if ($user->aAcces('seo_audit')) {
                $extraData['seo_audit'] = $this->scraper->auditSEO($urlSite);
            }
            if ($user->aAcces('tech_lookup')) {
                $extraData['tech_stack'] = $this->scraper->lookupTech($urlSite);
                
                // Fallback gratuit si Wappalyzer n'est pas configuré
                if (empty($extraData['tech_stack']) && !empty($extraData['seo_audit']['tech_detected'])) {
                    $extraData['tech_stack'] = $extraData['seo_audit']['tech_detected'];
                }
            }
        }

        // Étape 3 — Sentiment Analysis (Groq)
        if ($user->aAcces('sentiment_analysis') && !empty($context)) {
            $extraData['sentiment'] = $this->groq->analyserSentiment($context, $langue);
        }

        // Étape 3.5 — Recherche Concurrents (si autorisé)
        if ($user->aAcces('competitors')) {
            $qConc = $langue === 'en' ? "Top competitors of {$nom}" : "Principaux concurrents de {$nom}";
            $extraData['competitor_search'] = $this->scraper->search($qConc, $langue);
            if (!empty($extraData['competitor_search']['organic_results'])) {
                $context .= "\n\nConcurrents potentiels trouvés :\n" . json_encode($extraData['competitor_search']['organic_results']);
            }
        }

        // Étape 4 — Groq analyse stratégique & recommandations
        $analyse = $this->groq->analyserCroissance($recherche, $langue, $context);

        // Étape 5 — Sauvegarde
        $company = $this->sauvegarder($recherche, $analyse, $user, $extraData);

        $user->incrementAnalyses();
        Cache::put($cacheKey, $company, $ttl);

        return $company;
    }

    private function sauvegarder(array $r, array $a, User $user, array $extra = []): Company
    {
        $company = Company::updateOrCreate(
            ['slug' => Str::slug($r['nom'] ?? 'entreprise') . '-' . $user->id],
            [
                'user_id'          => $user->id,
                'nom'              => $r['nom'] ?? 'Inconnu',
                'url_site'         => $r['presence_web']['site_web_url'] ?? null,
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
            'extra_data'      => $extra,
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
