<?php

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

    public function evolutionDigital(): int
    {
        $avant = $this->snapshots()->orderByDesc('prise_le')->skip(1)->first();
        if (!$avant) return 0;
        return $this->score_digital - $avant->score_digital;
    }
}
