<?php

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
