<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.scrapingbee.key');
    }

    /**
     * Recherche Google via ScrapingBee
     */
    public function search(string $query, string $lang = 'fr'): array
    {
        if (empty($this->apiKey)) {
            Log::warning('ScrapingBee API key missing. Skipping web search.');
            return [];
        }

        try {
            $response = Http::get('https://app.scrapingbee.com/api/v1/google', [
                'api_key' => $this->apiKey,
                'search'  => $query,
                'language'=> $lang,
                'nb_results' => 5,
            ]);

            if ($response->failed()) {
                Log::error('ScrapingBee search failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('ScraperService Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Audit SEO simplifié via Google Lighthouse API (PageSpeed Insights)
     */
    public function auditSEO(string $url): array
    {
        $apiKey = config('services.lighthouse.key');
        if (empty($apiKey)) return ['error' => 'No API Key'];

        try {
            $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";
            $response = Http::get($apiUrl, [
                'url'      => $url,
                'category' => ['SEO', 'PERFORMANCE', 'ACCESSIBILITY'],
                'key'      => $apiKey
            ]);

            if ($response->failed()) return [];

            $data = $response->json();
            $lighthouse = $data['lighthouseResult'] ?? null;

            if (!$lighthouse) return [];

            // Extraction des technos depuis Lighthouse (Gratuit)
            $techs = [];
            $detectedTech = $lighthouse['audits']['detected-technologies'] ?? null;
            if ($detectedTech && isset($detectedTech['details']['items'])) {
                foreach ($detectedTech['details']['items'] as $item) {
                    $techs[] = [
                        'name' => $item['name'] ?? 'Inconnu',
                        'icon' => null, // Lighthouse ne fournit pas d'icônes
                        'categories' => [$item['category'] ?? 'others']
                    ];
                }
            }

            return [
                'performance'   => ($lighthouse['categories']['performance']['score'] ?? 0) * 100,
                'seo'           => ($lighthouse['categories']['seo']['score'] ?? 0) * 100,
                'accessibility' => ($lighthouse['categories']['accessibility']['score'] ?? 0) * 100,
                'best_practices'=> ($lighthouse['categories']['best-practices']['score'] ?? 0) * 100,
                'tech_detected' => $techs,
                'audits'        => $lighthouse['audits'] ?? [],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Détection techno (Wappalyzer ou fallback gratuit Lighthouse/WhatCMS)
     */
    public function lookupTech(string $url): array
    {
        // 1. Tenter Wappalyzer (si clé présente)
        $waKey = config('services.wappalyzer.key');
        if (!empty($waKey)) {
            try {
                $response = Http::withHeaders(['x-api-key' => $waKey])->get('https://api.wappalyzer.com/v2/lookup/', ['urls' => $url]);
                if ($response->successful()) return $response->json()[0]['technologies'] ?? [];
            } catch (\Exception $e) {}
        }

        // 2. Tenter WhatCMS (si clé présente - 500 free/mo)
        $wcKey = config('services.whatcms.key');
        if (!empty($wcKey)) {
            try {
                $response = Http::get('https://whatcms.org/API/Tech', ['key' => $wcKey, 'url' => $url]);
                if ($response->successful()) {
                    $data = $response->json();
                    $res = [];
                    foreach ($data['results'] ?? [] as $t) {
                        $res[] = ['name' => $t['name'], 'categories' => [$t['category']]];
                    }
                    return $res;
                }
            } catch (\Exception $e) {}
        }

        // 3. Fallback : Utiliser les données déjà récupérées par Lighthouse lors de l'audit SEO
        // Ou relancer un mini audit Lighthouse si besoin (mais coûteux en temps)
        return [];
    }
}
