<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Competitor;
use App\Services\AI\GeminiService;
use App\Services\AI\GroqService;

class CompetitorService
{
    public function __construct(
        private GeminiService $gemini,
        private GroqService   $groq,
    ) {}

    public function analyserConcurrents(Company $company): array
    {
        // Étape 1 — Gemini identifie les concurrents via Google
        $liste = $this->identifierConcurrents($company);

        // Étape 2 — Pour chaque concurrent, récupérer des infos
        $concurrents = [];
        foreach (array_slice($liste, 0, 4) as $nomConc) {
            try {
                $infos = $this->gemini->rechercherEntreprise($nomConc, $company->langue_detectee ?? 'fr');
                $conc  = Competitor::updateOrCreate(
                    ['company_id' => $company->id, 'nom' => $infos['nom'] ?? $nomConc],
                    [
                        'secteur'       => $infos['secteur'] ?? null,
                        'score_digital' => $infos['score_digital'] ?? 0,
                        'presence_web'  => $infos['presence_web'] ?? [],
                        'points_forts'  => $infos['points_forts'] ?? [],
                    ]
                );
                $concurrents[] = $conc;
            } catch (\Throwable) {
                continue;
            }
        }

        // Étape 3 — Groq fait l'analyse comparative
        $this->analyseComparative($company, $concurrents);

        return $concurrents;
    }

    private function identifierConcurrents(Company $company): array
    {
        $prompt = "Donne les noms des 4 principaux concurrents directs de \"{$company->nom}\" dans le secteur {$company->secteur} au {$company->pays}. Retourne uniquement un JSON : {\"concurrents\": [\"Nom1\",\"Nom2\",\"Nom3\",\"Nom4\"]}";

        $url = config('ai.gemini.base_url') . '/models/' . config('ai.gemini.model') . ':generateContent?key=' . config('ai.gemini.api_key');

        $response = \Illuminate\Support\Facades\Http::timeout(30)->post($url, [
            'contents'    => [['parts' => [['text' => $prompt]], 'role' => 'user']],
            'generationConfig' => ['responseMimeType' => 'application/json'],
            'tools'       => [['googleSearch' => (object)[]]],
        ]);

        $texte = $response->json('candidates.0.content.parts.0.text', '{"concurrents":[]}');
        $texte = trim(preg_replace('/^```json\s*|```\s*$/i', '', $texte));
        $data  = json_decode($texte, true);

        return $data['concurrents'] ?? [];
    }

    private function analyseComparative(Company $company, array $concurrents): void
    {
        if (empty($concurrents)) return;

        $donnees = [
            'entreprise_cible' => [
                'nom'           => $company->nom,
                'score_digital' => $company->score_digital,
                'presence_web'  => $company->presence_web,
            ],
            'concurrents' => array_map(fn($c) => [
                'nom'           => $c->nom,
                'score_digital' => $c->score_digital,
                'presence_web'  => $c->presence_web,
            ], $concurrents),
        ];

        $json = json_encode($donnees, JSON_UNESCAPED_UNICODE);

        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->withHeaders(['Authorization' => 'Bearer ' . config('ai.groq.api_key')])
            ->post(config('ai.groq.base_url') . '/chat/completions', [
                'model'    => config('ai.groq.model'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Analyste business. JSON uniquement.'],
                    ['role' => 'user',   'content' => "Comparaison concurrentielle : {$json}. Retourne {\"analyse_comparative\": \"texte\", \"avantages_competitifs\": [], \"menaces\": []}"],
                ],
            ]);

        $texte = $response->json('choices.0.message.content', '{}');
        $texte = trim(preg_replace('/^```json\s*|```\s*$/i', '', $texte));
        $data  = json_decode($texte, true);

        foreach ($concurrents as $conc) {
            $conc->update(['analyse_comparative' => $data['analyse_comparative'] ?? '']);
        }
    }
}
