@extends('layouts.app')

@section('title', 'USER_DASHBOARD')

@section('content')
<div class="max-w-6xl mx-auto space-y-12 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 anim-fade-up">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-primary-500 shadow-[0_0_8px_#00FF88]"></div>
                <div class="font-mono text-[10px] text-primary-500 uppercase tracking-[0.3em]">USER_SESSION_ACTIVE // {{ auth()->user()->name }}</div>
            </div>
            <h1 class="text-6xl font-display text-white tracking-tight">MON_ESPACE</h1>
            <p class="text-muted font-light italic text-lg max-w-xl">Bienvenue dans votre centre de commande analytique. Gérez vos crédits et accédez à vos rapports stratégiques.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('analysis.index') }}" class="btn-primary px-8 py-4">
                NOUVEL_AUDIT_IA
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 anim-fade-up" style="animation-delay: 0.1s">
        {{-- Plan Actuel --}}
        @php $plan = config('plans.' . auth()->user()->plan) @endphp
        <div class="card p-8 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-primary-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">LICENCE_ACTUELLE</div>
            <div class="text-3xl font-display text-white mb-2 uppercase">{{ $plan['label'] ?? 'Gratuit' }}</div>
            <div class="flex items-center gap-2">
                <a href="{{ route('subscription.index') }}" class="text-[10px] font-mono text-primary-500 hover:text-white transition uppercase tracking-widest">[UPGRADE_ACCOUNT]</a>
            </div>
        </div>

        {{-- Analyses Restantes --}}
        <div class="card p-8 relative overflow-hidden group">
             <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-cyan-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
            </div>
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">CRÉDITS_SESSION</div>
            <div class="text-3xl font-display text-white mb-2">
                @if(($plan['analyses_limit'] ?? 0) === -1)
                    ILLIMITÉ
                @else
                    {{ max(0, ($plan['analyses_limit'] ?? 3) - auth()->user()->analyses()->whereMonth('created_at', now()->month)->count()) }} 
                    <span class="text-sm text-muted">/ {{ $plan['analyses_limit'] ?? 3 }}</span>
                @endif
            </div>
            <div class="text-[10px] font-mono text-muted uppercase tracking-widest">Renouvellement le {{ now()->endOfMonth()->format('d/m') }}</div>
        </div>

        {{-- Rapports Générés --}}
        <div class="card p-8 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            </div>
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">ARCHIVES_SYSTÈME</div>
            <div class="text-3xl font-display text-white mb-2">{{ auth()->user()->analyses()->count() }}</div>
            <div class="flex items-center gap-2">
                <a href="{{ route('analysis.index') }}#recent" class="text-[10px] font-mono text-amber-500 hover:text-white transition uppercase tracking-widest">[VOIR_HISTORIQUE]</a>
            </div>
        </div>
    </div>

    {{-- Quick Actions / Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 anim-fade-up" style="animation-delay: 0.2s">
        {{-- Profile Raccourci --}}
        <div class="card p-10 flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-2xl font-display text-white uppercase tracking-wider">CONFIG_UTILISATEUR</h3>
                <p class="text-sm text-muted font-light leading-relaxed">Mettez à jour vos protocoles de sécurité, changez votre clé d'accès ou modifiez vos informations d'identité.</p>
            </div>
            <div class="mt-8">
                <a href="{{ route('profile.edit') }}" class="btn-ghost inline-block px-8 py-4 text-[10px] tracking-[0.2em]">
                    ACCÉDER_AUX_PARAMÈTRES
                </a>
            </div>
        </div>

        {{-- Status Système --}}
        <div class="card p-10 bg-gradient-to-br from-primary-500/5 to-transparent border-primary-500/10">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-3 h-3 rounded-full bg-primary-500 animate-pulse"></div>
                <h3 class="text-2xl font-display text-white uppercase tracking-wider text-primary-500">SYSTEM_STATUS: ONLINE</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="font-mono text-[10px] text-muted uppercase">IA_CORE_ENGINE</span>
                    <span class="font-mono text-[10px] text-primary-500 uppercase">OPTIMAL</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="font-mono text-[10px] text-muted uppercase">SYNC_DATABASE</span>
                    <span class="font-mono text-[10px] text-primary-500 uppercase">ACTIVE</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="font-mono text-[10px] text-muted uppercase">SSL_ENCRYPTION</span>
                    <span class="font-mono text-[10px] text-primary-500 uppercase">AES_256BIT</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
