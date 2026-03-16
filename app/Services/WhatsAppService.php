<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private string $phoneId;
    private string $apiUrl;

    public function __construct()
    {
        $this->token   = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
        $this->apiUrl  = "https://graph.facebook.com/v19.0/{$this->phoneId}/messages";
    }

    public function envoyerResume(Company $company, string $numero): bool
    {
        $analyse  = $company->derniereAnalyse();
        $message  = $this->construireMessage($company, $analyse);

        $response = Http::withToken($this->token)->post($this->apiUrl, [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliserNumero($numero),
            'type'              => 'text',
            'text'              => ['body' => $message],
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp send error', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        }

        return true;
    }

    private function construireMessage(Company $company, $analyse): string
    {
        $recos = collect($analyse?->recommandations ?? [])
            ->take(3)
            ->map(fn($r) => "• {$r['titre']}")
            ->implode("\n");

        return <<<MSG
🔍 *Analyse Business — {$company->nom}*

📊 Score digital : {$company->score_digital}/100
📈 Potentiel croissance : {$company->score_croissance}/100
🏢 Secteur : {$company->secteur}
📍 Localisation : {$company->pays}

*Top recommandations :*
{$recos}

Rapport complet disponible sur Business Intelligence Analyzer.
MSG;
    }

    private function normaliserNumero(string $numero): string
    {
        $numero = preg_replace('/[\s\-\(\)]/', '', $numero);
        if (!str_starts_with($numero, '+')) {
            $numero = '+' . $numero;
        }
        return $numero;
    }
}
