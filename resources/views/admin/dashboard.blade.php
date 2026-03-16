@extends('layouts.app')
@section('title', 'Admin - Terminal de Contrôle')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="space-y-12 anim-fade-up">
    {{-- Header --}}
    <div class="flex items-end justify-between border-b border-muted2 pb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="admin-badge">ROOT ACCESS</span>
                <span class="font-mono text-[10px] text-muted tracking-widest uppercase">// SECURE_SESSION: ACTIVE</span>
            </div>
            <h1 class="text-4xl font-display text-white">ADMIN <span class="text-red-500">DASHBOARD</span></h1>
        </div>
        <div class="font-mono text-[10px] text-muted text-right">
            SYSTEM_CPU: <span class="text-primary-500">0.12%</span><br>
            LATENCY: <span class="text-primary-500">24ms</span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach([
            ['label' => 'Utilisateurs',  'valeur' => $stats['total_users'], 'unit' => ' accounts'],
            ['label' => 'Analyses',      'valeur' => $stats['total_analyses'], 'unit' => ' scans'],
            ['label' => 'Tokens IA',     'valeur' => number_format($stats['total_tokens']), 'unit' => ' used'],
            ['label' => 'Plans Payants', 'valeur' => $stats['users_pro'], 'unit' => ' active'],
        ] as $stat)
        <div class="admin-stat-card lit">
            <div class="admin-label">{{ $stat['label'] }}</div>
            <div class="admin-value">{{ $stat['valeur'] }}<span class="text-[10px] text-muted font-mono uppercase ml-1">{{ $stat['unit'] }}</span></div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Top Secteurs --}}
        <div class="admin-section">
            <h3 class="admin-section-title">SECTEURS PRIORITAIRES</h3>
            <div class="space-y-2">
                @foreach($top_secteurs as $secteur)
                <div class="admin-list-item group">
                    <span class="font-mono text-xs text-muted group-hover:text-white transition uppercase">{{ $secteur->secteur ?? 'INDÉTERMINÉ' }}</span>
                    <span class="admin-val-badge">{{ $secteur->total }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Répartition par plan --}}
        <div class="admin-section">
            <h3 class="admin-section-title">MATRICE DES ABONNEMENTS</h3>
            <div class="space-y-2">
                @foreach(config('plans') as $code => $plan)
                @php $count = $parPlan[$code] ?? 0 @endphp
                <div class="admin-list-item group">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $code === 'pro' ? 'bg-primary-500' : ($code === 'agency' ? 'bg-amber-500' : 'bg-muted2') }}"></div>
                        <span class="font-mono text-xs text-muted group-hover:text-white transition uppercase">{{ $plan['label'] }}</span>
                    </div>
                    <span class="font-mono text-xs font-bold text-white tracking-widest">{{ $count }} USERS</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Graphique Analyses --}}
    <div class="admin-chart-container">
        <div class="flex items-center justify-between mb-8">
            <h3 class="font-display text-2xl text-white">ACTIVITÉ SCANS <span class="text-primary-500 text-lg ml-2">30 JRS</span></h3>
            <div class="flex gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-red-500"></div>
                    <span class="font-mono text-[9px] text-muted uppercase">Analyses / Jour</span>
                </div>
            </div>
        </div>
        <div class="h-[300px]">
            <canvas id="admin-chart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('admin-chart').getContext('2d');
    
    // Gradient pour le graphique
    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(255, 77, 77, 0.4)');
    grad.addColorStop(1, 'rgba(255, 77, 77, 0.05)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! $analyses_par_jour->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toJson() !!},
            datasets: [{
                label: 'Analyses',
                data: {!! $analyses_par_jour->pluck('total')->toJson() !!},
                backgroundColor: grad,
                borderColor: '#FF4D4D',
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: '#FF4D4D',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#070C18',
                    borderColor: 'rgba(255, 77, 77, 0.3)',
                    borderWidth: 1,
                    titleFont: { family: 'DM Mono', size: 11 },
                    bodyFont: { family: 'DM Mono', size: 11 },
                    cornerRadius: 4,
                    padding: 12
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.03)', drawBorder: false },
                    ticks: { 
                        color: '#5A6B82', 
                        font: { family: 'DM Mono', size: 9 },
                        callback: function(value) { return value + ' SCANS'; }
                    }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: '#5A6B82', font: { family: 'DM Mono', size: 9 } }
                }
            }
        }
    });
});
</script>
@endsection
