@extends('layouts.app')
@section('title', 'Mon Espace - Dashboard')

@section('content')
<div class="space-y-12 anim-fade-up">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-muted2 pb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-2 h-2 rounded-full bg-primary-500 shadow-[0_0_8px_#00FF88]"></div>
                <span class="font-mono text-[10px] text-muted tracking-widest uppercase">// SESSION_USER: {{ strtoupper(auth()->user()->name) }}</span>
            </div>
            <h1 class="text-4xl font-display text-white">TABLEAU DE <span class="text-primary-500">BORD</span></h1>
            <p class="font-mono text-[11px] text-muted mt-2 uppercase tracking-wide">Gestion des analyses et renseignements business</p>
        </div>
        <a href="{{ route('analysis.index') }}" class="btn-primary">
            + NOUVELLE ANALYSE
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['label' => 'Total Analyses', 'valeur' => $stats['total_analyses'], 'sub' => 'SCANS EFFECTUÉS'],
            ['label' => 'Analyses / Mois', 'valeur' => $stats['analyses_ce_mois'], 'sub' => 'PERIODE ACTUELLE'],
            ['label' => 'Score Moyen', 'valeur' => round($stats['score_moyen']) . '%', 'sub' => 'QUALITÉ DIGITALE'],
            ['label' => 'Dernier Rapport', 'valeur' => $stats['derniere_analyse'] ?? '—', 'sub' => 'DATE D\'ACTION'],
        ] as $index => $stat)
        <div class="card p-6 group relative overflow-hidden anim-fade-up" style="animation-delay: {{ $index * 0.1 }}s">
            <div class="absolute top-0 left-0 w-1 h-0 bg-primary-500 transition-all duration-500 group-hover:h-full"></div>
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-2">{{ $stat['label'] }}</div>
            <div class="text-3xl font-display text-white mb-1 tracking-wider">{{ $stat['valeur'] }}</div>
            <div class="font-mono text-[8px] text-muted2 uppercase tracking-widest">{{ $stat['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Quota Section --}}
    @php
        $planConfig = config('plans.' . auth()->user()->plan);
        $limite     = $planConfig['analyses_limit'];
        $pct        = $limite === -1 ? 0 : min(100, (auth()->user()->analyses_this_month / $limite) * 100);
    @endphp
    <div class="card p-8 relative overflow-hidden anim-fade-up" style="animation-delay: 0.4s">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-xl font-display text-white tracking-widest uppercase">Utilisation du Quota</h3>
                    <span class="nav-plan-badge">PLAN {{ strtoupper($planConfig['label']) }}</span>
                </div>
                <p class="font-mono text-[10px] text-muted uppercase tracking-widest">Calculé sur la base de votre cycle de facturation actuel</p>
            </div>
            <a href="{{ route('subscription.upgrade') }}" class="font-mono text-[10px] text-primary-500 hover:text-white transition tracking-widest uppercase border-b border-primary-500/30 pb-1">
                Optimiser mon plan →
            </a>
        </div>
        
        @if($limite === -1)
            <div class="p-6 border border-primary-500/20 bg-dim rounded-lg">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded bg-primary-500/10 flex items-center justify-center font-mono text-primary-500">∞</div>
                    <div>
                        <div class="text-white font-display text-lg tracking-widest uppercase">ACCÈS ILLIMITÉ DÉVERROUILLÉ</div>
                        <div class="font-mono text-[9px] text-muted uppercase">Aucune restriction sur le nombre d'analyses mensuelles</div>
                    </div>
                </div>
            </div>
        @else
            <div class="space-y-4">
                <div class="flex justify-between font-mono text-[10px] tracking-widest mb-1">
                    <span class="text-white">{{ auth()->user()->analyses_this_month }} ANALYSES</span>
                    <span class="text-muted2">LIMITE : {{ $limite }} SCANS / MOIS</span>
                </div>
                <div class="w-full bg-ink3 h-1.5 rounded-full overflow-hidden border border-bord3">
                    <div class="bg-primary-500 h-full rounded-full shadow-[0_0_8px_#00FF88] transition-all duration-1000" style="width: {{ $pct }}%"></div>
                </div>
                <div class="flex justify-start">
                    <span class="font-mono text-[9px] {{ $pct > 80 ? 'text-red-400' : 'text-muted' }} uppercase tracking-widest">
                        {{ $pct > 80 ? 'ATTENTION : Quota presque épuisé' : 'Status : Opérationnel' }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Liste des entreprises --}}
    <div class="card overflow-hidden anim-fade-up" style="animation-delay: 0.5s">
        <div class="p-8 border-b border-bord3 flex items-center justify-between bg-ink3/30">
            <h3 class="text-xl font-display text-white tracking-widest uppercase">Archive des Analyses</h3>
            <span class="font-mono text-[9px] text-muted uppercase tracking-widest">{{ $companies->total() }} ENTITÉS DÉTECTÉES</span>
        </div>
        
        <div class="divide-y divide-bord3">
            @forelse($companies as $company)
            <a href="{{ route('analysis.show', $company->slug) }}" class="flex items-center justify-between p-8 hover:bg-white/[0.02] transition group">
                <div class="flex items-center gap-8">
                    <div class="w-16 h-16 bg-ink3 border border-bord3 rounded flex items-center justify-center font-display text-2xl text-muted group-hover:text-primary-500 group-hover:border-primary-500/50 transition duration-500">
                        {{ substr($company->nom, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-xl font-display text-white tracking-wide group-hover:text-primary-500 transition">{{ $company->nom }}</div>
                        <div class="font-mono text-[10px] text-muted uppercase tracking-wider mt-1">
                            {{ $company->secteur ?? 'Secteur Inconnu' }} <span class="mx-2 text-muted2">|</span> {{ $company->pays ?? 'Global' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-12">
                    <div class="hidden sm:block text-right">
                        <div class="font-mono text-[9px] text-muted2 uppercase tracking-[0.2em] mb-2">Score Digital</div>
                        <div class="text-2xl font-display {{ $company->score_digital >= 60 ? 'text-primary-500 shadow-[0_0_15px_rgba(0,255,136,0.1)]' : 'text-amber-500' }}">
                            {{ $company->score_digital }}%
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full border border-bord3 flex items-center justify-center group-hover:border-primary-500 group-hover:bg-dim transition duration-500">
                        <svg class="w-4 h-4 text-muted2 group-hover:text-primary-500 transition transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </a>
            @empty
            <div class="p-20 text-center">
                <div class="font-mono text-xs text-muted mb-6 uppercase tracking-widest">Base de données vide</div>
                <a href="{{ route('analysis.index') }}" class="btn-ghost">
                    LANCER LE PREMIER SCAN
                </a>
            </div>
            @endforelse
        </div>
        
        @if($companies->hasPages())
        <div class="p-6 bg-ink2/50 border-t border-bord3 font-mono text-xs">
            {{ $companies->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
