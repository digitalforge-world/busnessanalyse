@php
    $snapshots = $company->snapshots;
    $labels    = $snapshots->map(fn($s) => $s->prise_le->format('d M'))->toJson();
    $digital   = $snapshots->pluck('score_digital')->toJson();
    $croissance = $snapshots->pluck('score_croissance')->toJson();
    $prog = app(\App\Services\SnapshotService::class)->calculerProgression($company);
@endphp

<div class="card p-10 space-y-12">
    @if($snapshots->count() < 2)
        <div class="flex flex-col items-center justify-center py-16 text-center space-y-6">
            <div class="w-16 h-16 rounded-full bg-muted2/10 flex items-center justify-center border border-muted2/20">
                <svg class="w-8 h-8 text-muted2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div class="space-y-2">
                <div class="font-mono text-[10px] text-muted uppercase tracking-[0.3em]">DATA_INSUFFICIENT</div>
                <p class="text-muted2 text-sm font-light max-w-sm mx-auto">
                    Le protocole de suivi nécessite au moins deux points d'analyse. Relancez un scan dans 7 jours.
                </p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3">
                <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-8 flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-primary-500 shadow-[0_0_8px_#00FF88]"></div> PERFORMANCE_LOGS_VISUALIZATION
                </div>
                <div class="h-[300px]">
                    <canvas id="evolution-chart"></canvas>
                </div>
            </div>

            <div class="space-y-6">
                <div class="font-mono text-[10px] text-muted uppercase tracking-widest mb-4">DELTA_ANALYTICS</div>
                @foreach([
                    ['DIGITAL_INDEX', $prog['evolution_digital'], 'primary'],
                    ['GROWTH_FLOW', $prog['evolution_croissance'], 'cyan']
                ] as $ev)
                @php $pos = $ev[1] >= 0; @endphp
                <div class="p-5 bg-muted2/5 border border-muted2/20 rounded-xl group hover:border-{{ $ev[2] }}-500/30 transition">
                    <div class="font-mono text-[9px] text-muted2 uppercase tracking-widest mb-2">{{ $ev[0] }}</div>
                    <div class="flex items-end justify-between">
                        <div class="text-3xl font-display {{ $pos ? 'text-'.$ev[2].'-500' : 'text-red-400' }}">
                            {{ $pos ? '+' : '' }}{{ $ev[1] }}<span class="text-sm opacity-50 ml-1">PTS</span>
                        </div>
                        <div class="mb-1">
                            <svg class="w-5 h-5 {{ $pos ? 'text-'.$ev[2].'-500' : 'text-red-400 rotate-180' }} transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('evolution-chart').getContext('2d');
            const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradient1.addColorStop(0, 'rgba(0, 255, 136, 0.15)');
            gradient1.addColorStop(1, 'rgba(0, 255, 136, 0)');

            const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradient2.addColorStop(0, 'rgba(0, 229, 255, 0.15)');
            gradient2.addColorStop(1, 'rgba(0, 229, 255, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! $labels !!},
                    datasets: [
                        {
                            label: 'DIGITAL_KPI',
                            data: {!! $digital !!},
                            borderColor: '#00FF88',
                            backgroundColor: gradient1,
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#00FF88',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#00FF88'
                        },
                        {
                            label: 'GROWTH_KPI',
                            data: {!! $croissance !!},
                            borderColor: '#00E5FF',
                            backgroundColor: gradient2,
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#00E5FF',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#00E5FF'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                color: '#6B7A90',
                                font: { size: 10, family: 'DM Mono' },
                                boxWidth: 10,
                                padding: 25
                            } 
                        },
                        tooltip: {
                            backgroundColor: '#0A0E17',
                            borderColor: 'rgba(0,255,136,0.2)',
                            borderWidth: 1,
                            titleColor: '#00FF88',
                            titleFont: { family: 'DM Mono', size: 12 },
                            bodyFont: { family: 'DM Mono', size: 11 },
                            padding: 12,
                            cornerRadius: 4,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: { 
                            min: 0, max: 100, 
                            grid: { color: 'rgba(255,255,255,0.03)' },
                            ticks: { color: '#3D4A5C', font: { family: 'DM Mono', size: 9 } } 
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#3D4A5C', font: { family: 'DM Mono', size: 9 } } 
                        }
                    }
                }
            });
        });
        </script>
    @endif
</div>
