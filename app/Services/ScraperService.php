<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    private ?string $scrapingBeeKey;
    private ?string $serpApiKey;

    public function __construct()
    {
        $this->scrapingBeeKey = config('services.scrapingbee.key');
        $this->serpApiKey      = config('services.serpapi.key');
    }

    /**
     * Recherche Google via ScrapingBee ou SerpApi
     */
    public function search(string $query, string $lang = 'fr'): array
    {
        // 1. Tenter SerpApi
        if (!empty($this->serpApiKey)) {
            try {
                $response = Http::get('https://serpapi.com/search', [
                    'api_key' => $this->serpApiKey,
                    'q'       => $query,
                    'hl'      => $lang,
                    'engine'  => 'google',
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    // Normalisation pour le frontend (SerpApi utilise 'link' au lieu de 'url')
                    if (isset($json['organic_results'])) {
                        foreach ($json['organic_results'] as &$res) {
                            if (!isset($res['url']) && isset($res['link'])) $res['url'] = $res['link'];
                        }
                    }
                    return $json;
                }
            } catch (\Exception $e) {
                Log::error('SerpApi search failed: ' . $e->getMessage());
            }
        }

        // 2. Fallback ou Alternative via ScrapingBee
        if (!empty($this->scrapingBeeKey)) {
            try {
                $response = Http::get('https://app.scrapingbee.com/api/v1/google', [
                    'api_key' => $this->scrapingBeeKey,
                    'search'  => $query,
                    'language'=> $lang,
                    'nb_results' => 5,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error('ScrapingBee search failed: ' . $e->getMessage());
            }
        }

        Log::warning('No Search API keys configured or search failed.');
        return [];
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
                'category' => ['SEO', 'PERFORMANCE', 'ACCESSIBILITY', 'BEST_PRACTICES'],
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
                        'icon' => null, 
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
                        $res[] = [
                            'name' => $t['name'] ?? 'Inconnu', 
                            'categories' => [$t['category'] ?? 'CMS'],
                            'version' => $t['version'] ?? null
                        ];
                    }
                    return $res;
                }
            } catch (\Exception $e) {}
        }

        return [];
    }
}
