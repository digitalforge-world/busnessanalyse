# Business Intelligence Analyzer — Laravel + Blade
## SaaS mondial — Google Gemini + Groq + Toutes fonctionnalités

> Application d'analyse d'entreprise mondiale : nom d'entreprise → recherche IA complète → rapport PDF → recommandations de croissance → suivi dans le temps.

---

## Sommaire

1. [Architecture & Stack](#1-architecture--stack)
2. [Installation & Packages](#2-installation--packages)
3. [Configuration](#3-configuration)
4. [Migrations & Modèles](#4-migrations--modèles)
5. [Authentification & Abonnements](#5-authentification--abonnements)
6. [Services IA](#6-services-ia)
7. [Export PDF](#7-export-pdf)
8. [Analyse Concurrents](#8-analyse-concurrents)
9. [Multi-langue](#9-multi-langue)
10. [Suivi dans le temps](#10-suivi-dans-le-temps)
11. [WhatsApp Business](#11-whatsapp-business)
12. [Controllers](#12-controllers)
13. [Routes](#13-routes)
14. [Vues Blade](#14-vues-blade)
15. [Dashboard Admin](#15-dashboard-admin)
16. [Jobs & Cache](#16-jobs--cache)
17. [Paiements](#17-paiements)
18. [Tests](#18-tests)
19. [Déploiement](#19-déploiement)

---

## 1. Architecture & Stack

```
business-analyzer/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AnalysisController.php
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── AdminController.php
│   │   │   ├── PdfController.php
│   │   │   ├── CompetitorController.php
│   │   │   └── SubscriptionController.php
│   │   └── Middleware/
│   │       ├── CheckAnalysisQuota.php
│   │       └── AdminOnly.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Company.php
│   │   ├── Analysis.php
│   │   ├── Competitor.php
│   │   ├── AnalysisSnapshot.php      ← suivi évolution
│   │   └── Subscription.php
│   ├── Services/
│   │   ├── AI/
│   │   │   ├── GeminiService.php     ← recherche web (Google Grounding)
│   │   │   └── GroqService.php       ← analyse & recommandations
│   │   ├── AnalysisService.php       ← orchestration principale
│   │   ├── CompetitorService.php     ← analyse concurrents
│   │   ├── PdfService.php            ← export rapport PDF
│   │   ├── WhatsAppService.php       ← WhatsApp Business API
│   │   ├── TranslationService.php    ← multi-langue IA
│   │   └── SnapshotService.php       ← suivi dans le temps
│   └── Jobs/
│       ├── RunCompanyAnalysis.php
│       ├── SendWhatsAppReport.php
│       └── TakeAnalysisSnapshot.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── analysis/
│   │   │   ├── index.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── partials/
│   │   ├── pdf/rapport.blade.php     ← template PDF
│   │   ├── dashboard/index.blade.php
│   │   └── admin/dashboard.blade.php
│   └── lang/
│       ├── fr/analysis.php
│       ├── en/analysis.php
│       └── ar/analysis.php
└── database/migrations/
```

### Choix des IAs

| IA | Rôle | Quota gratuit |
|----|------|---------------|
| **Google Gemini 1.5 Flash** | Recherche web temps réel (Google Grounding), collecte des infos | 15 req/min, 1M tokens/jour |
| **Groq LLaMA 3.3 70B** | Analyse approfondie, recommandations, scoring sectoriel | 6000 tokens/min, illimité |

---

## 2. Installation & Packages

```bash
composer create-project laravel/laravel business-analyzer
cd business-analyzer
```

### Packages essentiels

```bash
# HTTP & IA
composer require guzzlehttp/guzzle

# Auth complète (login, register, reset password)
composer require laravel/breeze
php artisan breeze:install blade
npm install && npm run dev

# PDF brandé (DomPDF — bien supporté Laravel)
composer require barryvdh/laravel-dompdf

# Markdown vers HTML (rendu IA dans Blade)
composer require erusev/parsedown

# Paiements
composer require stripe/stripe-php
composer require cinetpay/cinetpay-php    # CinetPay Afrique

# Queue & Jobs
php artisan queue:table

# Rate limiting API
composer require spatie/laravel-rate-limited-job-middleware

# Logs activité
composer require spatie/laravel-activitylog

# Traduction
composer require laravel/localization    # inclus nativement

# Charts dashboard (via CDN, pas npm)
# Chart.js chargé côté Blade via CDN
```

```bash
php artisan migrate
```

### `.env`

```env
APP_NAME="Business Intelligence Analyzer"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=business_analyzer
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# === GOOGLE GEMINI (gratuit) ===
# Clé sur : https://aistudio.google.com/app/apikey
GEMINI_API_KEY=your_gemini_api_key
GEMINI_MODEL=gemini-1.5-flash
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta

# === GROQ (gratuit) ===
# Clé sur : https://console.groq.com
GROQ_API_KEY=your_groq_api_key
GROQ_MODEL=llama-3.3-70b-versatile
GROQ_BASE_URL=https://api.groq.com/openai/v1

# === STRIPE (paiements intl.) ===
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx

# === CINETPAY (Mobile Money Afrique) ===
CINETPAY_API_KEY=your_cinetpay_key
CINETPAY_SITE_ID=your_site_id

# === WHATSAPP BUSINESS API ===
# Via Meta ou un provider (Twilio, 360dialog)
WHATSAPP_TOKEN=your_whatsapp_token
WHATSAPP_PHONE_ID=your_phone_number_id

# Cache analyses (minutes)
ANALYSIS_CACHE_TTL=1440

# Langue par défaut
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
```

---

## 3. Configuration

### `config/ai.php`

```php
<?php

return [

    'gemini' => [
        'api_key'   => env('GEMINI_API_KEY'),
        'model'     => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'base_url'  => env('GEMINI_BASE_URL'),
        'timeout'   => 60,
        'grounding' => true,   // Active Google Search en temps réel
    ],

    'groq' => [
        'api_key'    => env('GROQ_API_KEY'),
        'model'      => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'base_url'   => env('GROQ_BASE_URL'),
        'timeout'    => 45,
        'max_tokens' => 2048,
    ],

    'analysis' => [
        'cache_ttl'  => env('ANALYSIS_CACHE_TTL', 1440),
        'max_retries' => 2,
    ],

];
```

### `config/plans.php`

```php
<?php

return [

    'free' => [
        'label'          => 'Gratuit',
        'analyses_limit' => 3,       // par mois
        'pdf_export'     => false,
        'competitors'    => false,
        'whatsapp'       => false,
        'history'        => false,
        'price_usd'      => 0,
    ],

    'starter' => [
        'label'          => 'Starter',
        'analyses_limit' => 30,
        'pdf_export'     => true,
        'competitors'    => true,
        'whatsapp'       => false,
        'history'        => true,
        'price_usd'      => 10,
        'stripe_price_id' => env('STRIPE_STARTER_PRICE_ID'),
    ],

    'pro' => [
        'label'          => 'Pro',
        'analyses_limit' => -1,      // illimité
        'pdf_export'     => true,
        'competitors'    => true,
        'whatsapp'       => true,
        'history'        => true,
        'price_usd'      => 29,
        'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
    ],

    'agency' => [
        'label'          => 'Agency',
        'analyses_limit' => -1,
        'pdf_export'     => true,
        'competitors'    => true,
        'whatsapp'       => true,
        'history'        => true,
        'price_usd'      => 79,
        'stripe_price_id' => env('STRIPE_AGENCY_PRICE_ID'),
    ],

];
```

---

## 4. Migrations & Modèles

### Migration : `users` (extension Breeze)

```bash
php artisan make:migration add_plan_to_users_table
```

```php
<?php
// database/migrations/xxxx_add_plan_to_users_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('plan', ['free', 'starter', 'pro', 'agency'])->default('free');
            $table->integer('analyses_this_month')->default(0);
            $table->timestamp('analyses_reset_at')->nullable();
            $table->string('locale', 5)->default('fr');    // langue préférée
            $table->string('whatsapp_number', 20)->nullable();
            $table->boolean('is_admin')->default(false);
        });
    }
};
```

### Migration : `companies`

```bash
php artisan make:migration create_companies_table
```

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('secteur')->nullable();
            $table->string('pays')->nullable();
            $table->string('langue_detectee', 5)->default('fr'); // fr, en, ar, pt...
            $table->text('description')->nullable();
            $table->year('annee_fondation')->nullable();
            $table->enum('taille', ['TPE', 'PME', 'Grande entreprise'])->nullable();
            $table->integer('score_digital')->default(0);
            $table->integer('score_croissance')->default(0);
            $table->json('presence_web')->nullable();
            $table->json('points_forts')->nullable();
            $table->json('points_faibles')->nullable();
            $table->json('opportunites')->nullable();
            $table->timestamps();
        });
    }
};
```

### Migration : `analyses`

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['gemini_search', 'groq_analysis', 'full'])->default('full');
            $table->longText('analyse_ia')->nullable();
            $table->json('recommandations')->nullable();
            $table->json('plan_action')->nullable();
            $table->enum('statut', ['pending', 'running', 'done', 'failed'])->default('pending');
            $table->string('ia_utilisee')->nullable();
            $table->integer('tokens_utilises')->default(0);
            $table->string('langue', 5)->default('fr');
            $table->timestamps();
        });
    }
};
```

### Migration : `competitors`

```bash
php artisan make:migration create_competitors_table
```

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('secteur')->nullable();
            $table->integer('score_digital')->default(0);
            $table->integer('score_croissance')->default(0);
            $table->json('presence_web')->nullable();
            $table->json('points_forts')->nullable();
            $table->text('analyse_comparative')->nullable();
            $table->timestamps();
        });
    }
};
```

### Migration : `analysis_snapshots` (suivi évolution)

```bash
php artisan make:migration create_analysis_snapshots_table
```

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->integer('score_digital');
            $table->integer('score_croissance');
            $table->json('presence_web')->nullable();
            $table->text('note')->nullable();        // note de l'évolution
            $table->timestamp('prise_le');
            $table->timestamps();
        });
    }
};
```

### Migration : `subscriptions`

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['free', 'starter', 'pro', 'agency'])->default('free');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('cinetpay_transaction_id')->nullable();
            $table->enum('statut', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamp('expire_le')->nullable();
            $table->timestamps();
        });
    }
};
```

```bash
php artisan migrate
```

### Modèle : `User`

```php
<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password',
        'plan', 'analyses_this_month', 'analyses_reset_at',
        'locale', 'whatsapp_number', 'is_admin',
    ];

    protected $casts = [
        'analyses_reset_at' => 'datetime',
        'is_admin'          => 'boolean',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    // Vérifie si l'utilisateur peut encore faire une analyse ce mois
    public function peutAnalyser(): bool
    {
        $config = config("plans.{$this->plan}");
        $limite = $config['analyses_limit'];

        if ($limite === -1) return true; // illimité

        // Reset mensuel
        if (!$this->analyses_reset_at || $this->analyses_reset_at->isPast()) {
            $this->update([
                'analyses_this_month' => 0,
                'analyses_reset_at'   => now()->addMonth(),
            ]);
            return true;
        }

        return $this->analyses_this_month < $limite;
    }

    public function incrementAnalyses(): void
    {
        $this->increment('analyses_this_month');
    }

    public function aAcces(string $feature): bool
    {
        return config("plans.{$this->plan}.{$feature}", false);
    }
}
```

### Modèle : `Company`

```php
<?php
// app/Models/Company.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'user_id', 'nom', 'slug', 'secteur', 'pays', 'langue_detectee',
        'description', 'annee_fondation', 'taille',
        'score_digital', 'score_croissance',
        'presence_web', 'points_forts', 'points_faibles', 'opportunites',
    ];

    protected $casts = [
        'presence_web'   => 'array',
        'points_forts'   => 'array',
        'points_faibles' => 'array',
        'opportunites'   => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            $company->slug = Str::slug($company->nom) . '-' . uniqid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(Competitor::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(AnalysisSnapshot::class)->orderBy('prise_le');
    }

    public function derniereAnalyse(): ?Analysis
    {
        return $this->analyses()->latest()->first();
    }

    public function niveauDigital(): string
    {
        return match(true) {
            $this->score_digital >= 80 => 'Excellent',
            $this->score_digital >= 60 => 'Bon',
            $this->score_digital >= 40 => 'Moyen',
            default                    => 'Faible',
        };
    }

    // Evolution du score digital par rapport au dernier snapshot
    public function evolutionDigital(): int
    {
        $avant = $this->snapshots()->orderByDesc('prise_le')->skip(1)->first();
        if (!$avant) return 0;
        return $this->score_digital - $avant->score_digital;
    }
}
```

### Modèle : `Analysis`

```php
<?php
// app/Models/Analysis.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    protected $fillable = [
        'company_id', 'type', 'analyse_ia', 'recommandations',
        'plan_action', 'statut', 'ia_utilisee', 'tokens_utilises', 'langue',
    ];

    protected $casts = [
        'recommandations' => 'array',
        'plan_action'     => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
```

### Modèle : `Competitor`

```php
<?php
// app/Models/Competitor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    protected $fillable = [
        'company_id', 'nom', 'secteur', 'score_digital',
        'score_croissance', 'presence_web', 'points_forts', 'analyse_comparative',
    ];

    protected $casts = [
        'presence_web' => 'array',
        'points_forts' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
```

### Modèle : `AnalysisSnapshot`

```php
<?php
// app/Models/AnalysisSnapshot.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisSnapshot extends Model
{
    protected $fillable = [
        'company_id', 'score_digital', 'score_croissance',
        'presence_web', 'note', 'prise_le',
    ];

    protected $casts = [
        'presence_web' => 'array',
        'prise_le'     => 'datetime',
    ];
}
```

---

## 5. Authentification & Abonnements

### Middleware : `CheckAnalysisQuota`

```bash
php artisan make:middleware CheckAnalysisQuota
```

```php
<?php
// app/Http/Middleware/CheckAnalysisQuota.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAnalysisQuota
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if (!$user->peutAnalyser()) {
            $plan = config("plans.{$user->plan}");
            return response()->json([
                'success' => false,
                'message' => "Quota atteint ({$plan['analyses_limit']} analyses/mois). Passez au plan supérieur.",
                'upgrade' => true,
            ], 429);
        }

        return $next($request);
    }
}
```

```php
// app/Http/Kernel.php — enregistrer le middleware
protected $routeMiddleware = [
    // ...
    'quota' => \App\Http\Middleware\CheckAnalysisQuota::class,
    'admin' => \App\Http\Middleware\AdminOnly::class,
];
```

### Middleware : `AdminOnly`

```php
<?php
// app/Http/Middleware/AdminOnly.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()?->is_admin) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
```

---

## 6. Services IA

### `GeminiService.php` — Recherche web avec Google Grounding

```php
<?php
// app/Services/AI/GeminiService.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('ai.gemini.api_key');
        $this->model   = config('ai.gemini.model');
        $this->baseUrl = config('ai.gemini.base_url');
    }

    public function rechercherEntreprise(string $nom, string $langue = 'fr'): array
    {
        $prompt = $this->construirePrompt($nom, $langue);

        $body = [
            'contents' => [['parts' => [['text' => $prompt]], 'role' => 'user']],
            'generationConfig' => [
                'temperature'     => 0.2,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json',
            ],
            'systemInstruction' => [
                'parts' => [[
                    'text' => 'Tu es un analyste business. Réponds UNIQUEMENT en JSON valide, sans backtick, sans texte avant ou après.',
                ]],
            ],
            // Google Search Grounding : accès web temps réel
            'tools' => [['googleSearch' => (object)[]]],
        ];

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(60)->post($url, $body);

        if ($response->failed()) {
            Log::error('Gemini error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException("Gemini API error: {$response->status()}");
        }

        $texte = $response->json('candidates.0.content.parts.0.text', '');

        return $this->parseJSON($texte);
    }

    private function construirePrompt(string $nom, string $langue): string
    {
        return <<<PROMPT
Recherche sur le web l'entreprise "{$nom}". Retourne un JSON :
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
        $texte = trim(preg_replace('/^```json\s*|```\s*$/i', '', $texte));
        $data  = json_decode($texte, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Gemini JSON parse error: ' . json_last_error_msg());
        }

        return $data;
    }
}
```

### `GroqService.php` — Analyse complète + Recommandations

```php
<?php
// app/Services/AI/GroqService.php

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
        $json   = json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
                    ['role' => 'system', 'content' => 'Tu es un consultant business senior. Réponds UNIQUEMENT en JSON valide.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Groq error', ['status' => $response->status()]);
            throw new \RuntimeException("Groq API error: {$response->status()}");
        }

        $texte = $response->json('choices.0.message.content', '');
        $tokens = $response->json('usage.total_tokens', 0);

        $resultat = $this->parseJSON($texte);
        $resultat['_tokens'] = $tokens;

        return $resultat;
    }

    private function langueInstruction(string $langue): string
    {
        return match($langue) {
            'en' => 'Respond with the analyse_ia field in English.',
            'ar' => 'Écris le champ analyse_ia en arabe.',
            'pt' => 'Escreva o campo analyse_ia em português.',
            default => 'Écris le champ analyse_ia en français.',
        };
    }

    private function parseJSON(string $texte): array
    {
        $texte = trim(preg_replace('/^```json\s*|```\s*$/i', '', $texte));
        $data  = json_decode($texte, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Groq JSON parse error: ' . json_last_error_msg());
        }

        return $data;
    }
}
```

### `AnalysisService.php` — Orchestration

```php
<?php
// app/Services/AnalysisService.php

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
```

---

## 7. Export PDF

### `PdfService.php`

```bash
php artisan make:class Services/PdfService
```

```php
<?php
// app/Services/PdfService.php

namespace App\Services;

use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function genererRapport(Company $company): string
    {
        $analyse = $company->derniereAnalyse();

        $pdf = Pdf::loadView('pdf.rapport', [
            'company'  => $company,
            'analyse'  => $analyse,
            'date'     => now()->format('d/m/Y'),
        ])
        ->setPaper('A4', 'portrait')
        ->setOptions([
            'defaultFont'  => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 150,
        ]);

        $nomFichier = 'rapport-' . $company->slug . '-' . now()->format('Ymd') . '.pdf';
        $chemin     = storage_path("app/public/rapports/{$nomFichier}");

        if (!file_exists(dirname($chemin))) {
            mkdir(dirname($chemin), 0755, true);
        }

        $pdf->save($chemin);

        return $nomFichier;
    }
}
```

### `PdfController.php`

```bash
php artisan make:controller PdfController
```

```php
<?php
// app/Http/Controllers/PdfController.php

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
```

### Template PDF : `resources/views/pdf/rapport.blade.php`

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; }

        .header {
            background: #0F6E56;
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header .date { font-size: 10px; opacity: 0.8; }
        .badge-plan {
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9px;
        }

        .section { padding: 20px 32px; border-bottom: 1px solid #f0f0f0; }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0F6E56;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .kpi-row { display: flex; gap: 12px; margin-bottom: 16px; }
        .kpi { background: #f7f7f7; padding: 12px; border-radius: 8px; flex: 1; text-align: center; }
        .kpi-label { font-size: 9px; color: #666; margin-bottom: 4px; }
        .kpi-val { font-size: 18px; font-weight: bold; color: #0F6E56; }

        .tag { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; margin: 2px; }
        .tag-green { background: #E1F5EE; color: #0F6E56; }
        .tag-red { background: #FAECE7; color: #993C1D; }
        .tag-amber { background: #FAEEDA; color: #854F0B; }

        .reco-item { padding: 8px 0; border-bottom: 1px solid #f5f5f5; }
        .reco-title { font-weight: bold; font-size: 11px; }
        .reco-desc { font-size: 10px; color: #555; margin-top: 2px; }
        .priority-haute { color: #D85A30; font-size: 9px; }
        .priority-moyenne { color: #BA7517; font-size: 9px; }
        .priority-faible { color: #1D9E75; font-size: 9px; }

        .analyse-text { font-size: 11px; line-height: 1.7; color: #333; }

        .score-bar { background: #eee; height: 6px; border-radius: 3px; margin-top: 4px; }
        .score-fill { height: 6px; border-radius: 3px; background: #1D9E75; }
        .score-fill-blue { height: 6px; border-radius: 3px; background: #378ADD; }

        .footer {
            background: #f7f7f7;
            padding: 16px 32px;
            text-align: center;
            font-size: 9px;
            color: #999;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <div>
            <div class="header-brand">Business Intelligence Analyzer</div>
            <h1>{{ $company->nom }}</h1>
            <div class="date">Rapport généré le {{ $date }}</div>
        </div>
        <div>
            <div class="badge-plan">Rapport complet</div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="section">
        <div class="section-title">Vue d'ensemble</div>
        <div class="kpi-row">
            <div class="kpi">
                <div class="kpi-label">Secteur</div>
                <div class="kpi-val" style="font-size:13px">{{ $company->secteur ?? '—' }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Localisation</div>
                <div class="kpi-val" style="font-size:13px">{{ $company->pays ?? '—' }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Score digital</div>
                <div class="kpi-val">{{ $company->score_digital }}<span style="font-size:10px">/100</span></div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Potentiel croissance</div>
                <div class="kpi-val">{{ $company->score_croissance }}<span style="font-size:10px">/100</span></div>
            </div>
        </div>
        <p style="font-size:11px;color:#444;line-height:1.6">{{ $company->description }}</p>
    </div>

    <!-- Présence digitale -->
    <div class="section">
        <div class="section-title">Présence digitale</div>
        @php
            $labels = ['site_web'=>'Site web','facebook'=>'Facebook','instagram'=>'Instagram','linkedin'=>'LinkedIn','twitter'=>'Twitter/X','whatsapp_business'=>'WhatsApp Business','tiktok'=>'TikTok','youtube'=>'YouTube'];
            $presence = $company->presence_web ?? [];
        @endphp
        @foreach($labels as $key => $label)
        <span class="tag {{ ($presence[$key] ?? false) ? 'tag-green' : 'tag-red' }}">
            {{ ($presence[$key] ?? false) ? '✓' : '✗' }} {{ $label }}
        </span>
        @endforeach
        <div style="margin-top:8px">
            <div style="font-size:10px;color:#666">Score digital</div>
            <div class="score-bar"><div class="score-fill" style="width:{{ $company->score_digital }}%"></div></div>
        </div>
    </div>

    <!-- Forces / Faiblesses -->
    <div class="section">
        <div class="section-title">Forces & Opportunités</div>
        @foreach(($company->points_forts ?? []) as $point)
            <span class="tag tag-green">✓ {{ $point }}</span>
        @endforeach
        @foreach(($company->points_faibles ?? []) as $point)
            <span class="tag tag-red">✗ {{ $point }}</span>
        @endforeach
        @foreach(($company->opportunites ?? []) as $opp)
            <span class="tag tag-amber">↗ {{ $opp }}</span>
        @endforeach
    </div>

    <!-- Recommandations -->
    <div class="section">
        <div class="section-title">Plan d'action recommandé</div>
        @foreach(($analyse?->recommandations ?? []) as $reco)
        <div class="reco-item">
            <div class="reco-title">{{ $reco['titre'] ?? '' }}</div>
            <div class="reco-desc">{{ $reco['description'] ?? '' }}</div>
            <div class="priority-{{ $reco['priorite'] ?? 'faible' }}">
                ● Priorité {{ $reco['priorite'] ?? '' }} — ROI : {{ $reco['roi_estime'] ?? '—' }}
            </div>
        </div>
        @endforeach
    </div>

    <!-- Analyse IA -->
    <div class="section">
        <div class="section-title">Analyse IA — Potentiel de croissance</div>
        <p class="analyse-text">{{ $analyse?->analyse_ia ?? '' }}</p>
        <div style="margin-top:10px">
            <div style="font-size:10px;color:#666">Potentiel de croissance</div>
            <div class="score-bar"><div class="score-fill-blue" style="width:{{ $company->score_croissance }}%"></div></div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Business Intelligence Analyzer • Rapport confidentiel • {{ $date }}
    </div>

</body>
</html>
```

---

## 8. Analyse Concurrents

### `CompetitorService.php`

```bash
php artisan make:class Services/CompetitorService
```

```php
<?php
// app/Services/CompetitorService.php

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
```

---

## 9. Multi-langue

### Détection et adaptation automatique

```php
<?php
// app/Services/TranslationService.php

namespace App\Services;

class TranslationService
{
    // Langues supportées : fr, en, ar, pt, sw (swahili)
    private array $paysLangue = [
        // Afrique francophone
        'togo'        => 'fr', 'benin'       => 'fr', 'senegal'   => 'fr',
        'cote d\'ivoire' => 'fr', "côte d'ivoire" => 'fr',
        'cameroun'    => 'fr', 'mali'         => 'fr', 'guinee'    => 'fr',
        'burkina faso' => 'fr', 'niger'       => 'fr', 'congo'     => 'fr',
        'gabon'       => 'fr', 'rdc'          => 'fr',
        // Maghreb
        'maroc'       => 'ar', 'algerie'      => 'ar', 'tunisie'   => 'ar',
        // Afrique anglophone
        'nigeria'     => 'en', 'ghana'        => 'en', 'kenya'     => 'en',
        'south africa' => 'en', 'ouganda'     => 'en', 'tanzanie'  => 'sw',
        // Lusophone
        'mozambique'  => 'pt', 'angola'       => 'pt', 'cap-vert'  => 'pt',
        // Reste du monde
        'france'      => 'fr', 'belgique'     => 'fr',
        'usa'         => 'en', 'united states' => 'en',
        'uk'          => 'en', 'united kingdom' => 'en',
    ];

    public function detecterLangue(string $pays, string $langueUser = 'fr'): string
    {
        $pays  = strtolower(trim($pays));
        foreach ($this->paysLangue as $motCle => $langue) {
            if (str_contains($pays, $motCle)) {
                return $langue;
            }
        }
        // Fallback sur la langue de l'utilisateur
        return $langueUser;
    }
}
```

### Fichiers de traduction

```php
<?php
// resources/lang/fr/analysis.php
return [
    'title'           => 'Analyser une entreprise',
    'subtitle'        => 'Entrez un nom d\'entreprise pour obtenir une analyse complète.',
    'placeholder'     => 'Ex: Orange Togo, Ecobank...',
    'btn_analyze'     => 'Analyser',
    'score_digital'   => 'Score digital',
    'score_growth'    => 'Potentiel croissance',
    'tab_profile'     => 'Profil',
    'tab_digital'     => 'Présence digitale',
    'tab_reco'        => 'Recommandations',
    'tab_analysis'    => 'Analyse IA',
    'tab_competitors' => 'Concurrents',
    'tab_history'     => 'Évolution',
    'export_pdf'      => 'Télécharger le rapport PDF',
    'send_whatsapp'   => 'Envoyer sur WhatsApp',
    'quota_exceeded'  => 'Quota atteint. Passez au plan supérieur.',
];
```

```php
<?php
// resources/lang/en/analysis.php
return [
    'title'           => 'Analyze a Business',
    'subtitle'        => 'Enter a company name to get a complete analysis.',
    'placeholder'     => 'E.g: Orange Togo, Ecobank...',
    'btn_analyze'     => 'Analyze',
    'score_digital'   => 'Digital Score',
    'score_growth'    => 'Growth Potential',
    'tab_profile'     => 'Profile',
    'tab_digital'     => 'Digital Presence',
    'tab_reco'        => 'Recommendations',
    'tab_analysis'    => 'AI Analysis',
    'tab_competitors' => 'Competitors',
    'tab_history'     => 'Evolution',
    'export_pdf'      => 'Download PDF Report',
    'send_whatsapp'   => 'Send on WhatsApp',
    'quota_exceeded'  => 'Quota reached. Upgrade your plan.',
];
```

### Middleware de langue

```bash
php artisan make:middleware SetLocale
```

```php
<?php
// app/Http/Middleware/SetLocale.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->user()?->locale ?? session('locale', config('app.locale'));
        app()->setLocale($locale);
        return $next($request);
    }
}
```

```php
// Enregistrer dans app/Http/Kernel.php dans $middlewareGroups['web']
\App\Http\Middleware\SetLocale::class,
```

---

## 10. Suivi dans le temps

### `SnapshotService.php`

```php
<?php
// app/Services/SnapshotService.php

namespace App\Services;

use App\Models\AnalysisSnapshot;
use App\Models\Company;

class SnapshotService
{
    // Prend un snapshot si le dernier date de plus de 7 jours
    public function prendreSnapshot(Company $company): void
    {
        $dernier = $company->snapshots()->latest('prise_le')->first();

        if ($dernier && $dernier->prise_le->diffInDays(now()) < 7) {
            return; // Trop récent
        }

        AnalysisSnapshot::create([
            'company_id'      => $company->id,
            'score_digital'   => $company->score_digital,
            'score_croissance' => $company->score_croissance,
            'presence_web'    => $company->presence_web,
            'prise_le'        => now(),
        ]);
    }

    // Calcule la progression entre le premier et le dernier snapshot
    public function calculerProgression(Company $company): array
    {
        $snapshots = $company->snapshots()->orderBy('prise_le')->get();

        if ($snapshots->count() < 2) {
            return ['evolution_digital' => 0, 'evolution_croissance' => 0, 'snapshots' => $snapshots];
        }

        $premier = $snapshots->first();
        $dernier = $snapshots->last();

        return [
            'evolution_digital'    => $dernier->score_digital - $premier->score_digital,
            'evolution_croissance' => $dernier->score_croissance - $premier->score_croissance,
            'snapshots'            => $snapshots,
        ];
    }
}
```

### Partial Blade : graphique évolution

```blade
{{-- resources/views/analysis/partials/historique.blade.php --}}

@php
    $snapshots = $company->snapshots;
    $labels    = $snapshots->map(fn($s) => $s->prise_le->format('d/m/Y'))->toJson();
    $digital   = $snapshots->pluck('score_digital')->toJson();
    $croissance = $snapshots->pluck('score_croissance')->toJson();
@endphp

<div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
    @if($snapshots->count() < 2)
        <p class="text-sm text-gray-400 text-center py-4">
            Pas encore assez de données. Relancez une analyse dans 7 jours pour voir l'évolution.
        </p>
    @else
        <canvas id="evolution-chart" height="120"></canvas>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('evolution-chart'), {
                type: 'line',
                data: {
                    labels: {!! $labels !!},
                    datasets: [
                        {
                            label: 'Score digital',
                            data: {!! $digital !!},
                            borderColor: '#1D9E75',
                            backgroundColor: 'rgba(29,158,117,0.08)',
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: 'Potentiel croissance',
                            data: {!! $croissance !!},
                            borderColor: '#378ADD',
                            backgroundColor: 'rgba(55,138,221,0.08)',
                            tension: 0.4,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } },
                    scales: {
                        y: { min: 0, max: 100, ticks: { font: { size: 11 } } },
                        x: { ticks: { font: { size: 11 } } }
                    }
                }
            });
        });
        </script>
    @endif
</div>
```

---

## 11. WhatsApp Business

### `WhatsAppService.php`

```bash
php artisan make:class Services/WhatsAppService
```

```php
<?php
// app/Services/WhatsAppService.php

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

    // Envoie un résumé de l'analyse sur WhatsApp
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
        // Supprime espaces, tirets, parenthèses
        $numero = preg_replace('/[\s\-\(\)]/', '', $numero);
        // Ajoute le + si absent
        if (!str_starts_with($numero, '+')) {
            $numero = '+' . $numero;
        }
        return $numero;
    }
}
```

### Config WhatsApp

```php
// config/services.php — ajouter :
'whatsapp' => [
    'token'    => env('WHATSAPP_TOKEN'),
    'phone_id' => env('WHATSAPP_PHONE_ID'),
],
```

### Job WhatsApp

```php
<?php
// app/Jobs/SendWhatsAppReport.php

namespace App\Jobs;

use App\Models\Company;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Company $company,
        private string  $numero,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $whatsApp->envoyerResume($this->company, $this->numero);
    }
}
```

---

## 12. Controllers

### `AnalysisController.php` — Complet

```php
<?php
// app/Http/Controllers/AnalysisController.php

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
    ) {}

    public function index()
    {
        $user      = auth()->user();
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
        $company = Company::where('slug', $slug)->where('user_id', $user->id)->firstOrFail();

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
        $company = Company::where('slug', $slug)->where('user_id', $user->id)->firstOrFail();

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
}
```

### `DashboardController.php`

```bash
php artisan make:controller DashboardController
```

```php
<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user      = $request->user();
        $companies = $user->companies()->with('analyses')->latest()->paginate(10);

        $stats = [
            'total_analyses'    => $user->companies()->count(),
            'analyses_ce_mois'  => $user->analyses_this_month,
            'score_moyen'       => $user->companies()->avg('score_digital') ?? 0,
            'derniere_analyse'  => $user->companies()->latest()->first()?->nom,
        ];

        return view('dashboard.index', compact('user', 'companies', 'stats'));
    }
}
```

---

## 13. Routes

```php
<?php
// routes/web.php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Auth (généré par Breeze)
require __DIR__ . '/auth.php';

// Page d'accueil publique
Route::get('/', fn() => view('welcome'))->name('home');

// Changer la langue
Route::post('/langue/{locale}', function (string $locale) {
    $locales = ['fr', 'en', 'ar', 'pt'];
    if (in_array($locale, $locales)) {
        auth()->user()?->update(['locale' => $locale]);
        session(['locale' => $locale]);
    }
    return back();
})->name('langue.changer');

// Application principale (authentification requise)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analyser', [AnalysisController::class, 'index'])->name('analysis.index');

    // Analyse
    Route::post('/analyser', [AnalysisController::class, 'analyser'])
        ->middleware('quota')
        ->name('analysis.analyser');

    Route::get('/entreprise/{slug}', [AnalysisController::class, 'show'])->name('analysis.show');

    Route::post('/entreprise/{slug}/concurrents', [AnalysisController::class, 'analyserConcurrents'])
        ->name('analysis.concurrents');

    Route::post('/entreprise/{slug}/whatsapp', [AnalysisController::class, 'envoyerWhatsApp'])
        ->name('analysis.whatsapp');

    // PDF
    Route::get('/entreprise/{slug}/pdf', [PdfController::class, 'telecharger'])
        ->name('analysis.pdf');

    // Abonnements
    Route::get('/abonnement', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/abonnement/stripe', [SubscriptionController::class, 'stripe'])->name('subscription.stripe');
    Route::post('/abonnement/cinetpay', [SubscriptionController::class, 'cinetpay'])->name('subscription.cinetpay');
    Route::get('/abonnement/upgrade', fn() => view('subscription.upgrade'))->name('subscription.upgrade');

});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])->name('admin.users');
    Route::get('/analyses', [AdminController::class, 'analyses'])->name('admin.analyses');
});
```

---

## 14. Vues Blade

### Layout principal : `layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Business Intelligence Analyzer')</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Chart.js (pour les graphiques d'évolution) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .loader-dot { animation: bounce 1.4s infinite; }
        .loader-dot:nth-child(2) { animation-delay: 0.2s; }
        .loader-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce { 0%,80%,100%{transform:scale(0)} 40%{transform:scale(1)} }
        .rtl { direction: rtl; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

    <nav class="bg-white border-b border-gray-200 px-6 py-3">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('analysis.index') }}" class="text-base font-semibold text-gray-900">
                Business Intelligence
            </a>
            <div class="flex items-center gap-4">
                {{-- Sélecteur de langue --}}
                <div class="flex gap-1">
                    @foreach(['fr' => '🇫🇷', 'en' => '🇬🇧', 'ar' => '🇲🇦'] as $code => $flag)
                    <form method="POST" action="{{ route('langue.changer', $code) }}">
                        @csrf
                        <button type="submit" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === $code ? 'bg-green-100 text-green-800' : 'text-gray-400 hover:text-gray-700' }}">
                            {{ $flag }} {{ strtoupper($code) }}
                        </button>
                    </form>
                    @endforeach
                </div>

                @auth
                    {{-- Badge plan --}}
                    @php $plan = config('plans.' . auth()->user()->plan) @endphp
                    <span class="text-xs px-2 py-1 bg-green-50 text-green-800 rounded-full border border-green-200">
                        {{ $plan['label'] ?? 'Free' }}
                    </span>
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-400 hover:text-gray-700">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Connexion</a>
                    <a href="{{ route('register') }}" class="text-sm px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">S'inscrire</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @if(session('message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('message') }}
        </div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        Business Intelligence Analyzer — Powered by Gemini + Groq
    </footer>

</body>
</html>
```

### Dashboard utilisateur : `dashboard/index.blade.php`

```blade
@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    <p class="text-sm text-gray-500">Bienvenue, {{ auth()->user()->name }}</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Entreprises analysées', 'valeur' => $stats['total_analyses']],
        ['label' => 'Ce mois-ci',             'valeur' => $stats['analyses_ce_mois']],
        ['label' => 'Score digital moyen',    'valeur' => round($stats['score_moyen']) . '/100'],
        ['label' => 'Dernière analyse',       'valeur' => $stats['derniere_analyse'] ?? '—'],
    ] as $stat)
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <div class="text-xs text-gray-500 mb-1">{{ $stat['label'] }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stat['valeur'] }}</div>
    </div>
    @endforeach
</div>

{{-- Quota --}}
@php
    $planConfig = config('plans.' . auth()->user()->plan);
    $limite     = $planConfig['analyses_limit'];
    $pct        = $limite === -1 ? 0 : min(100, (auth()->user()->analyses_this_month / $limite) * 100);
@endphp
<div class="bg-white border border-gray-100 rounded-xl p-5 mb-6 shadow-sm">
    <div class="flex justify-between items-center mb-2">
        <span class="text-sm font-medium">Quota analyses — Plan {{ $planConfig['label'] }}</span>
        <a href="{{ route('subscription.upgrade') }}" class="text-xs text-green-600 hover:underline">Upgrader</a>
    </div>
    @if($limite === -1)
        <div class="text-sm text-green-600">Illimité</div>
    @else
        <div class="text-sm text-gray-600 mb-2">{{ auth()->user()->analyses_this_month }} / {{ $limite }} ce mois</div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
        </div>
    @endif
</div>

{{-- Historique des analyses --}}
<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="font-medium text-gray-900">Mes analyses</h2>
        <a href="{{ route('analysis.index') }}" class="text-sm text-green-600 hover:underline">+ Nouvelle analyse</a>
    </div>
    @forelse($companies as $company)
    <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex items-center justify-between hover:bg-gray-50">
        <div>
            <div class="text-sm font-medium text-gray-900">{{ $company->nom }}</div>
            <div class="text-xs text-gray-400">{{ $company->secteur }} • {{ $company->pays }}</div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-xs text-gray-400">Score digital</div>
                <div class="text-sm font-medium {{ $company->score_digital >= 60 ? 'text-green-600' : 'text-amber-600' }}">
                    {{ $company->score_digital }}/100
                </div>
            </div>
            <a href="{{ route('analysis.show', $company->slug) }}" class="text-xs text-gray-400 hover:text-gray-700 border border-gray-200 px-3 py-1 rounded-lg">
                Voir →
            </a>
        </div>
    </div>
    @empty
    <div class="px-5 py-8 text-center text-sm text-gray-400">
        Aucune analyse. <a href="{{ route('analysis.index') }}" class="text-green-600 hover:underline">Commencez ici.</a>
    </div>
    @endforelse
    <div class="px-5 py-3">{{ $companies->links() }}</div>
</div>
@endsection
```

---

## 15. Dashboard Admin

### `AdminController.php`

```bash
php artisan make:controller AdminController
```

```php
<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'     => User::count(),
            'total_analyses'  => Company::count(),
            'total_tokens'    => Analysis::sum('tokens_utilises'),
            'users_pro'       => User::whereIn('plan', ['pro', 'agency'])->count(),
        ];

        $parPlan = User::select('plan', DB::raw('count(*) as total'))
            ->groupBy('plan')
            ->pluck('total', 'plan');

        $top_secteurs = Company::select('secteur', DB::raw('count(*) as total'))
            ->whereNotNull('secteur')
            ->groupBy('secteur')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $top_pays = Company::select('pays', DB::raw('count(*) as total'))
            ->whereNotNull('pays')
            ->groupBy('pays')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $analyses_par_jour = Company::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('admin.dashboard', compact('stats', 'parPlan', 'top_secteurs', 'top_pays', 'analyses_par_jour'));
    }

    public function utilisateurs(Request $request)
    {
        $users = User::withCount('companies')
            ->latest()
            ->paginate(20);

        return view('admin.utilisateurs', compact('users'));
    }

    public function analyses(Request $request)
    {
        $analyses = Company::with(['user', 'analyses'])
            ->latest()
            ->paginate(20);

        return view('admin.analyses', compact('analyses'));
    }
}
```

### Vue Admin : `admin/dashboard.blade.php`

```blade
@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total utilisateurs',  'valeur' => $stats['total_users']],
        ['label' => 'Total analyses',       'valeur' => $stats['total_analyses']],
        ['label' => 'Tokens consommés',     'valeur' => number_format($stats['total_tokens'])],
        ['label' => 'Utilisateurs payants', 'valeur' => $stats['users_pro']],
    ] as $stat)
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <div class="text-xs text-gray-500 mb-1">{{ $stat['label'] }}</div>
        <div class="text-2xl font-semibold text-gray-900">{{ $stat['valeur'] }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Répartition par plan --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-medium text-gray-900 mb-4">Répartition par plan</h3>
        @foreach(config('plans') as $code => $plan)
        @php $count = $parPlan[$code] ?? 0 @endphp
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-600">{{ $plan['label'] }}</span>
            <span class="text-sm font-medium">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    {{-- Top secteurs --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-medium text-gray-900 mb-4">Top secteurs analysés</h3>
        @foreach($top_secteurs as $secteur)
        <div class="flex items-center justify-between mb-1">
            <span class="text-sm text-gray-600">{{ $secteur->secteur }}</span>
            <span class="text-sm font-medium">{{ $secteur->total }}</span>
        </div>
        @endforeach
    </div>

</div>

{{-- Graphique analyses par jour --}}
<div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
    <h3 class="text-sm font-medium text-gray-900 mb-4">Analyses des 30 derniers jours</h3>
    <canvas id="admin-chart" height="80"></canvas>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        new Chart(document.getElementById('admin-chart'), {
            type: 'bar',
            data: {
                labels: {!! $analyses_par_jour->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toJson() !!},
                datasets: [{
                    label: 'Analyses',
                    data: {!! $analyses_par_jour->pluck('total')->toJson() !!},
                    backgroundColor: 'rgba(29,158,117,0.7)',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
    </script>
</div>
@endsection
```

---

## 16. Jobs & Cache

### Job principal : `RunCompanyAnalysis.php`

```php
<?php
// app/Jobs/RunCompanyAnalysis.php

namespace App\Jobs;

use App\Models\Analysis;
use App\Models\User;
use App\Services\AnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunCompanyAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        private string $nomEntreprise,
        private int    $userId,
        private int    $analysisId,
    ) {}

    public function handle(AnalysisService $service): void
    {
        $analyse = Analysis::findOrFail($this->analysisId);
        $analyse->update(['statut' => 'running']);

        try {
            $user = User::findOrFail($this->userId);
            $service->analyserEntreprise($this->nomEntreprise, $user);
            $analyse->update(['statut' => 'done']);
        } catch (\Throwable $e) {
            $analyse->update(['statut' => 'failed']);
            throw $e;
        }
    }
}
```

### Commande : vider le cache

```bash
php artisan make:command ClearAnalysisCache
```

```php
<?php
// app/Console/Commands/ClearAnalysisCache.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearAnalysisCache extends Command
{
    protected $signature   = 'analysis:clear-cache';
    protected $description = 'Vide le cache des analyses d\'entreprise';

    public function handle(): void
    {
        Cache::flush();
        $this->info('Cache vidé.');
    }
}
```

---

## 17. Paiements

### `SubscriptionController.php`

```bash
php artisan make:controller SubscriptionController
```

```php
<?php
// app/Http/Controllers/SubscriptionController.php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = config('plans');
        $user  = auth()->user();
        return view('subscription.index', compact('plans', 'user'));
    }

    // Redirection vers Stripe Checkout (cartes internationales)
    public function stripe(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:starter,pro,agency']]);

        $plan = config("plans.{$request->plan}");

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price'    => $plan['stripe_price_id'],
                'quantity' => 1,
            ]],
            'mode'        => 'subscription',
            'success_url' => route('subscription.index') . '?success=1',
            'cancel_url'  => route('subscription.index') . '?cancelled=1',
            'metadata'    => ['user_id' => auth()->id(), 'plan' => $request->plan],
        ]);

        return redirect($session->url);
    }

    // CinetPay — Mobile Money (Togo, Côte d'Ivoire, Sénégal, etc.)
    public function cinetpay(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:starter,pro,agency']]);

        $plan   = config("plans.{$request->plan}");
        $user   = auth()->user();
        $transId = 'bia-' . $user->id . '-' . time();

        $montant = $plan['price_usd'] * 600; // Conversion USD → XOF approximative

        $payload = [
            'apikey'         => config('services.cinetpay.api_key'),
            'site_id'        => config('services.cinetpay.site_id'),
            'transaction_id' => $transId,
            'amount'         => $montant,
            'currency'       => 'XOF',
            'description'    => "Abonnement {$plan['label']} — Business Intelligence",
            'return_url'     => route('subscription.index') . '?success=1',
            'notify_url'     => route('subscription.cinetpay.webhook'),
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
        ];

        $response = \Illuminate\Support\Facades\Http::post('https://api-checkout.cinetpay.com/v2/payment', $payload);
        $data     = $response->json();

        if (isset($data['data']['payment_url'])) {
            return redirect($data['data']['payment_url']);
        }

        return back()->with('error', 'Erreur CinetPay. Réessayez.');
    }

    // Active l'abonnement après paiement réussi (webhook Stripe)
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, config('services.stripe.webhook_secret'));
        } catch (\Exception $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $userId  = $session->metadata->user_id;
            $plan    = $session->metadata->plan;

            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->update(['plan' => $plan]);
                Subscription::create([
                    'user_id'                => $userId,
                    'plan'                   => $plan,
                    'stripe_subscription_id' => $session->subscription,
                    'statut'                 => 'active',
                    'expire_le'              => now()->addMonth(),
                ]);
            }
        }

        return response('OK', 200);
    }
}
```

### Vue abonnements : `subscription/index.blade.php`

```blade
@extends('layouts.app')
@section('title', 'Abonnement')

@section('content')
<div class="text-center mb-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Choisissez votre plan</h1>
    <p class="text-gray-500">Paiement par carte (mondial) ou Mobile Money (Afrique)</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-5 max-w-5xl mx-auto">
    @foreach(config('plans') as $code => $plan)
    @php $actuel = $user->plan === $code @endphp
    <div class="bg-white border {{ $code === 'pro' ? 'border-green-400 ring-2 ring-green-400' : 'border-gray-200' }} rounded-2xl p-6 flex flex-col">
        @if($code === 'pro')
        <div class="text-center mb-3">
            <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">Recommandé</span>
        </div>
        @endif
        <div class="text-lg font-bold text-gray-900 mb-1">{{ $plan['label'] }}</div>
        <div class="text-3xl font-bold text-gray-900 mb-4">
            ${{ $plan['price_usd'] }}<span class="text-sm font-normal text-gray-400">/mois</span>
        </div>
        <ul class="text-sm text-gray-600 space-y-2 mb-6 flex-1">
            <li>{{ $plan['analyses_limit'] === -1 ? 'Illimité' : $plan['analyses_limit'] }} analyses/mois</li>
            <li class="{{ $plan['pdf_export'] ? 'text-gray-700' : 'text-gray-300 line-through' }}">Export PDF</li>
            <li class="{{ $plan['competitors'] ? 'text-gray-700' : 'text-gray-300 line-through' }}">Analyse concurrents</li>
            <li class="{{ $plan['whatsapp'] ? 'text-gray-700' : 'text-gray-300 line-through' }}">Envoi WhatsApp</li>
            <li class="{{ $plan['history'] ? 'text-gray-700' : 'text-gray-300 line-through' }}">Suivi évolution</li>
        </ul>

        @if($actuel)
        <div class="text-center text-sm text-green-600 font-medium py-2 border border-green-200 rounded-lg">Plan actuel</div>
        @elseif($plan['price_usd'] > 0)
        <div class="space-y-2">
            {{-- Stripe (carte internationale) --}}
            <form method="POST" action="{{ route('subscription.stripe') }}">
                @csrf
                <input type="hidden" name="plan" value="{{ $code }}">
                <button type="submit" class="w-full py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800 transition">
                    Payer par carte
                </button>
            </form>
            {{-- CinetPay (Mobile Money) --}}
            <form method="POST" action="{{ route('subscription.cinetpay') }}">
                @csrf
                <input type="hidden" name="plan" value="{{ $code }}">
                <button type="submit" class="w-full py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">
                    Flooz / TMoney / Wave
                </button>
            </form>
        </div>
        @else
        <div class="text-center text-sm text-gray-400 py-2">Gratuit</div>
        @endif
    </div>
    @endforeach
</div>
@endsection
```

---

## 18. Tests

```bash
php artisan make:test AnalysisServiceTest --unit
php artisan make:test AnalysisControllerTest
```

```php
<?php
// tests/Unit/AnalysisServiceTest.php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AI\GeminiService;
use App\Services\AI\GroqService;
use App\Services\AnalysisService;
use App\Services\SnapshotService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class AnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyse_retourne_une_company(): void
    {
        $user        = User::factory()->create(['plan' => 'pro', 'locale' => 'fr']);
        $geminiMock  = Mockery::mock(GeminiService::class);
        $groqMock    = Mockery::mock(GroqService::class);

        $geminiMock->shouldReceive('rechercherEntreprise')->once()->andReturn([
            'nom'           => 'Orange Togo',
            'secteur'       => 'Télécommunications',
            'pays'          => 'Lomé, Togo',
            'langue_detectee' => 'fr',
            'description'   => 'Opérateur mobile leader au Togo.',
            'taille'        => 'Grande entreprise',
            'score_digital' => 85,
            'presence_web'  => ['site_web' => true, 'facebook' => true],
            'points_forts'  => ['Large réseau', 'Mobile Money'],
            'points_faibles'=> ['Concurrence'],
            'opportunites'  => ['FinTech'],
        ]);

        $groqMock->shouldReceive('analyserCroissance')->once()->andReturn([
            'score_croissance' => 78,
            'analyse_ia'       => 'Fort potentiel.',
            'recommandations'  => [],
            'plan_action'      => [],
            '_tokens'          => 500,
        ]);

        $this->app->instance(GeminiService::class, $geminiMock);
        $this->app->instance(GroqService::class, $groqMock);

        $service = new AnalysisService($geminiMock, $groqMock);
        $company = $service->analyserEntreprise('Orange Togo', $user);

        $this->assertEquals('Orange Togo', $company->nom);
        $this->assertEquals(85, $company->score_digital);
        $this->assertEquals(78, $company->score_croissance);
    }

    public function test_quota_bloque_utilisateur_gratuit(): void
    {
        $user = User::factory()->create([
            'plan'                => 'free',
            'analyses_this_month' => 3,
            'analyses_reset_at'   => now()->addDays(20),
        ]);

        $this->assertFalse($user->peutAnalyser());
    }

    public function test_quota_illimite_plan_pro(): void
    {
        $user = User::factory()->create(['plan' => 'pro']);
        $this->assertTrue($user->peutAnalyser());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

---

## 19. Déploiement

### Commandes de mise en production

```bash
# Optimisations Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Migrations
php artisan migrate --force

# Stockage public (pour les PDFs générés)
php artisan storage:link

# Queue worker (garder actif en production)
php artisan queue:work --daemon --sleep=3 --tries=3

# Ou avec Supervisor (recommandé)
# /etc/supervisor/conf.d/laravel-worker.conf
# [program:laravel-worker]
# command=php /var/www/html/artisan queue:work --sleep=3 --tries=3
# autostart=true
# autorestart=true
```

### `.htaccess` (Apache — rediriger vers /public)

```apache
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public
RewriteRule ^(.*)$ /public/$1 [L]
```

### Cron (snapshots automatiques)

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Vide le cache des analyses chaque nuit
    $schedule->command('analysis:clear-cache')->daily();

    // Reset mensuel des quotas
    $schedule->call(function () {
        \App\Models\User::query()->update([
            'analyses_this_month' => 0,
            'analyses_reset_at'   => now()->addMonth(),
        ]);
    })->monthly();
}
```

```bash
# Ajouter au crontab du serveur
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

---

## Récapitulatif des commandes

```bash
# Installation complète
composer create-project laravel/laravel business-analyzer
cd business-analyzer
composer require guzzlehttp/guzzle barryvdh/laravel-dompdf erusev/parsedown stripe/stripe-php spatie/laravel-activitylog
php artisan breeze:install blade
npm install && npm run dev

# Artisan
php artisan make:migration create_companies_table
php artisan make:migration create_analyses_table
php artisan make:migration create_competitors_table
php artisan make:migration create_analysis_snapshots_table
php artisan make:migration create_subscriptions_table
php artisan make:migration add_plan_to_users_table
php artisan queue:table
php artisan migrate

# Lancer
php artisan serve
php artisan queue:work   # terminal séparé
```

---

## APIs gratuites — Liens

| Service | Lien | Quota |
|---------|------|-------|
| Google Gemini Flash | https://aistudio.google.com/app/apikey | 15 req/min, 1M tokens/j |
| Groq LLaMA 3.3 | https://console.groq.com | 6000 tokens/min |
| Stripe | https://dashboard.stripe.com | Gratuit en test |
| CinetPay | https://cinetpay.com/developers | Compte marchand |
| WhatsApp Business API | https://developers.facebook.com/docs/whatsapp | Compte Meta Business |

---

*Business Intelligence Analyzer v2.0 — DBS • Document technique complet*