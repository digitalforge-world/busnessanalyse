<?php

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
