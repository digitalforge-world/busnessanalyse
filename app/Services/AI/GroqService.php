<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('ai.groq.api_key');
        $this->model   = config('ai.groq.model');
        $this->baseUrl = config('ai.groq.base_url');
    }

    public function analyserCroissance(array $donnees, string $langue = 'fr'): array
    {
        $json               = json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $langue_instruction = $this->langueInstruction($langue);

        $prompt = <<<PROMPT
{$langue_instruction}
Données de l'entreprise :
{$json}

Génère un JSON :
{
  "recommandations": [
    {
      "titre": "...",
      "description": "2-3 phrases avec impact chiffré",
      "priorite": "haute",
      "categorie": "site_web",
      "roi_estime": "3-6 mois",
      "cout_estime": "Faible"
    }
  ],
  "score_croissance": 72,
  "analyse_ia": "6-8 phrases sur le potentiel de croissance et les leviers.",
  "plan_action": {
    "court_terme": ["action 1"],
    "moyen_terme": ["action 1"],
    "long_terme":  ["action 1"]
  },
  "scoring_sectoriel": {
    "score_entreprise": 0,
    "moyenne_secteur":  0,
    "top_secteur":      0,
    "commentaire":      "..."
  }
}
Règles : 5-7 recommandations, contexte africain/mondial adapté, Mobile Money si pertinent.
PROMPT;

        $response = Http::timeout(45)
            ->withHeaders(['Authorization' => "Bearer {$this->apiKey}"])
            ->post("{$this->baseUrl}/chat/completions", [
                'model'       => $this->model,
                'max_tokens'  => config('ai.groq.max_tokens', 2048),
                'temperature' => 0.3,
                'messages'    => [
                    ['role' => 'system', 'content' => 'Tu es un consultant business senior. Réponds UNIQUEMENT en JSON valide, sans backtick, sans texte avant ou après.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Groq error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException("Groq API error: {$response->status()}");
        }

        $texte  = $response->json('choices.0.message.content', '');
        $tokens = $response->json('usage.total_tokens', 0);

        $resultat            = $this->parseJSON($texte);
        $resultat['_tokens'] = $tokens;

        return $resultat;
    }

    private function langueInstruction(string $langue): string
    {
        return match($langue) {
            'en'    => 'Respond with the analyse_ia field in English.',
            'ar'    => 'Écris le champ analyse_ia en arabe.',
            'pt'    => 'Escreva o campo analyse_ia em português.',
            default => 'Écris le champ analyse_ia en français.',
        };
    }

    private function parseJSON(string $texte): array
    {
        // Supprimer les balises de code markdown
        $texte = preg_replace('/^```json\s*/i', '', $texte);
        $texte = preg_replace('/^```\s*/i', '', $texte);
        $texte = preg_replace('/```\s*$/i', '', $texte);
        $texte = trim($texte);

        // Extraire uniquement le bloc JSON entre { }
        if (preg_match('/\{.*\}/s', $texte, $matches)) {
            $texte = $matches[0];
        }

        $data = json_decode($texte, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Groq JSON parse error', [
                'erreur' => json_last_error_msg(),
                'texte'  => substr($texte, 0, 500),
            ]);
            throw new \RuntimeException('Groq JSON parse error: ' . json_last_error_msg());
        }

        return $data;
    }
}