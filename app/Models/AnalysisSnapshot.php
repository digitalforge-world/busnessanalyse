<?php

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
