<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl;

    private array $models = [
        'gemini-2.0-flash',
        'gemini-2.0-flash-lite',
    ];

    public function __construct()
    {
        $this->apiKey  = config('ai.gemini.api_key');
        $this->baseUrl = config('ai.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

        $modeleConfig = config('ai.gemini.model');
        if ($modeleConfig && $modeleConfig !== $this->models[0]) {
            array_unshift($this->models, $modeleConfig);
            $this->models = array_unique($this->models);
        }
    }

    public function rechercherEntreprise(string $nom, string $langue = 'fr', string $context = ''): array
    {
        $prompt = $this->construirePrompt($nom, $langue, $context);
        $body   = $this->construireBody($prompt);

        foreach ($this->models as $model) {
            $url      = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";
            $response = Http::timeout(60)->post($url, $body);

            if ($response->status() === 429) {
                Log::warning("Gemini quota dépassé [{$model}], tentative modèle suivant...");
                continue;
            }

            if ($response->failed()) {
                Log::error("Gemini error [{$model}]", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                continue;
            }

            Log::info("Gemini succès avec le modèle [{$model}]");
            $texte = $response->json('candidates.0.content.parts.0.text', '');
            return $this->parseJSON($texte);
        }

        Log::warning('Gemini totalement indisponible, bascule sur Groq...');
        return $this->rechercherViaGroq($prompt);
    }

    private function rechercherViaGroq(string $prompt): array
    {
        $apiKey  = config('ai.groq.api_key');
        $baseUrl = config('ai.groq.base_url', 'https://api.groq.com/openai/v1');
        $model   = 'llama-3.3-70b-versatile';

        if (empty($apiKey)) {
            throw new \RuntimeException('Gemini et Groq sont tous les deux indisponibles.');
        }

        $response = Http::timeout(60)
            ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
            ->post("{$baseUrl}/chat/completions", [
                'model'       => $model,
                'temperature' => 0.2,
                'max_tokens'  => 2048,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Tu es un analyste business expert. Réponds UNIQUEMENT en JSON valide, sans backtick, sans texte avant ou après.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Groq fallback error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Tous les services IA sont indisponibles. Réessaie dans quelques minutes.');
        }

        Log::info('Groq utilisé comme fallback Gemini avec succès.');
        $texte = $response->json('choices.0.message.content', '');
        return $this->parseJSON($texte);
    }

    private function construireBody(string $prompt): array
    {
        return [
            'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature'      => 0.2,
                'maxOutputTokens'  => 2048,
                'responseMimeType' => 'application/json',
            ],
            'systemInstruction' => [
                'parts' => [[
                    'text' => 'Tu es un analyste business expert. Réponds UNIQUEMENT en JSON valide, sans backtick, sans texte avant ou après.',
                ]],
            ],
        ];
    }

    private function construirePrompt(string $nom, string $langue, string $context = ''): string
    {
        $contextPart = $context ? "Voici des informations récentes trouvées sur le web :\n{$context}\n" : "";

        return <<<PROMPT
{$contextPart}
Analyse l'entreprise "{$nom}". Retourne un JSON structuré basé sur tes connaissances et le contexte fourni :
{
  "nom": "nom officiel",
  "secteur": "secteur d'activité",
  "pays": "pays et ville",
  "langue_detectee": "{$langue}",
  "description": "2 phrases max",
  "annee_fondation": "année ou null",
  "taille": "TPE ou PME ou Grande entreprise",
  "presence_web": {
    "site_web": true,
    "site_web_url": "URL du site si trouvée, sinon null",
    "facebook": false,
    "instagram": false,
    "linkedin": false,
    "twitter": false,
    "whatsapp_business": false,
    "tiktok": false,
    "youtube": false
  },
  "score_digital": 0,
  "points_forts": [],
  "points_faibles": [],
  "opportunites": [],
  "secteur_benchmark_score": 0
}
PROMPT;
    }

    private function parseJSON(string $texte): array
    {
        // Nettoyer les balises de code
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
            Log::error('JSON parse error', [
                'erreur' => json_last_error_msg(),
                'texte'  => substr($texte, 0, 500),
            ]);
            throw new \RuntimeException('JSON parse error: ' . json_last_error_msg());
        }

        return $data;
    }
}