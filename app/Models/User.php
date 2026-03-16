<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'plan', 'analyses_this_month', 'analyses_reset_at',
        'locale', 'whatsapp_number', 'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'analyses_reset_at' => 'datetime',
            'is_admin'          => 'boolean',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function peutAnalyser(): bool
    {
        $config = config("plans.{$this->plan}");
        $limite = $config['analyses_limit'];

        if ($limite === -1) return true;

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

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }
}
