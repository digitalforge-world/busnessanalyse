<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'plan', 'stripe_subscription_id',
        'cinetpay_transaction_id', 'statut', 'expire_le',
    ];

    protected $casts = [
        'expire_le' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
