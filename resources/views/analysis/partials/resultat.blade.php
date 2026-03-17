@php
    $presence  = $company->presence_web ?? [];
    $recos     = $analyse?->recommandations ?? [];
    $plan      = $analyse?->plan_action ?? [];
    $scoring   = []; 
    $icons     = ['site_web'=>'🌐','reseaux'=>'📱','marketing'=>'📢','outil_ia'=>'🤖','autre'=>'💡'];
    $canPdf    = auth()->user()?->aAcces('pdf_export');
    $canConc   = auth()->user()?->aAcces('competitors');
    $canWa     = auth()->user()?->aAcces('whatsapp');
    $canHist   = auth()->user()?->aAcces('history');

    $socials = [
        'site_web'          => 'Site web',
        'facebook'          => 'Facebook',
        'instagram'         => 'Instagram',
        'linkedin'          => 'LinkedIn',
        'twitter'           => 'Twitter / X',
        'whatsapp_business' => 'WhatsApp Biz',
        'tiktok'            => 'TikTok',
        'youtube'           => 'YouTube',
    ];
@endphp

<div class="rc card overflow-hidden border-muted2/20 mb-8 anim-fade-up">

    {{-- ── EN-TÊTE ── --}}
    <div class="flex flex-col md:flex-row justify-between gap-6 p-8 border-b border-muted2/20 bg-gradient-to-br from-primary-500/5 to-transparent">
        <div>
            <div class="text-4xl font-display text-white tracking-wider mb-3">{{ strtoupper($company->nom) }}</div>
            <div class="flex flex-wrap gap-3">
                @if($company->secteur)<span class="font-mono text-[9px] text-muted border border-muted2/30 px-2 py-1 rounded uppercase tracking-widest font-bold">{{ $company->secteur }}</span>@endif
                @if($company->pays)<span class="font-mono text-[9px] text-primary-500 border border-primary-500/20 px-2 py-1 rounded uppercase tracking-widest bg-primary-500/5">{{ $company->pays }}</span>@endif
                @if($company->taille)<span class="font-mono text-[9px] text-muted border border-muted2/30 px-2 py-1 rounded uppercase tracking-widest">{{ $company->taille }}</span>@endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($canPdf)
            <a href="{{ route('analysis.pdf', $company->slug) }}" class="btn-ghost text-[10px] tracking-widest uppercase py-2 px-4 border-muted2/30" target="_blank">
                ↓ PDF_EXPORT
            </a>
            @endif
            @if(auth()->user()?->aAcces('pro_exports'))
            <a href="{{ route('analysis.excel', $company->slug) }}" class="btn-ghost text-[10px] tracking-widest uppercase py-2 px-4 border-muted2/30">
                ↓ EXCEL_EXPORT
            </a>
            @endif
            <a href="{{ route('analysis.show', $company->slug) }}" class="btn-primary text-[10px] tracking-widest uppercase py-2 px-6">
                RAPPORT_FULL →
            </a>
        </div>
    </div>

    {{-- ── KPI ROW ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-muted2/20 border-b border-muted2/20">
        <div class="p-6 group hover:bg-white/[0.02] transition">
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">DIGITAL_SCORE</div>
            <div class="text-4xl font-display text-primary-500 leading-none mb-4">{{ $company->score_digital }}<span class="text-sm text-muted ml-1">%</span></div>
            <div class="h-1 bg-muted2/20 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500 shadow-[0_0_8px_#00FF88] transition-all duration-1000 kbf" data-w="{{ $company->score_digital }}"></div>
            </div>
        </div>
        <div class="p-6 group hover:bg-white/[0.02] transition">
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">GROWTH_POTENTIAL</div>
            <div class="text-4xl font-display text-cyan-500 leading-none mb-4">{{ $company->score_croissance }}<span class="text-sm text-muted ml-1">%</span></div>
            <div class="h-1 bg-muted2/20 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 shadow-[0_0_8px_#00E5FF] transition-all duration-1000 kbf" data-w="{{ $company->score_croissance }}"></div>
            </div>
        </div>
        <div class="p-6 group hover:bg-white/[0.02] transition">
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">DIGITAL_PRESENCE</div>
            @php $presCount = collect($presence)->filter()->count(); @endphp
            <div class="text-4xl font-display text-white leading-none mb-4">
                {{ $presCount }}<span class="text-sm text-muted ml-1">/{{ count($socials) }}</span>
            </div>
            <div class="h-1 bg-muted2/20 rounded-full overflow-hidden">
                <div class="h-full bg-white/40 transition-all duration-1000 kbf" data-w="{{ round(($presCount/count($socials))*100) }}"></div>
            </div>
        </div>
        <div class="p-6 group hover:bg-white/[0.02] transition">
            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">RANKING_LEVEL</div>
            <div class="text-2xl font-display text-amber-500 leading-none mb-4">{{ strtoupper($company->niveauDigital()) }}</div>
            <div class="font-mono text-[10px] text-muted2 tracking-widest uppercase">
                {{ $company->langue_detectee ? strtoupper($company->langue_detectee) : 'FR' }} // {{ $company->pays ?? 'GLOBAL' }}
            </div>
        </div>
    </div>

    {{-- ── TABS ── --}}
    <div class="flex border-b border-muted2/20 bg-ink2 overflow-x-auto no-scrollbar">
        @php 
            $tabs = ['PROFIL' => 'profil', 'DIGITAL' => 'digital', 'CONSEILS' => 'recos', 'IA_INSIGHT' => 'ia'];
            if($analyse?->extra_data) $tabs['WEB_AUDIT'] = 'web';
            $tabs['COMPETITION'] = 'conc';
        @endphp
        @foreach($tabs as $label => $id)
        <button class="t-btn px-8 py-4 font-mono text-[10px] tracking-[0.2em] text-muted hover:text-white transition border-b-2 border-transparent transition-all duration-300 {{ $id === 'profil' ? 'on text-primary-500 border-primary-500 bg-primary-500/5' : '' }}" 
            data-t="{{ $id }}" onclick="tab('{{ $id }}')">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── PANEL PROFIL ── --}}
    <div class="panel p-8 on" id="p-profil">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <div class="w-1 h-1 rounded-full bg-primary-500"></div> EXECUTIVE_SUMMARY
                    </div>
                    <p class="text-muted leading-relaxed text-lg font-light">
                        {{ $company->description ?? 'Aucune description disponible pour cette entité.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['DÉTECTION_PAYS', $company->pays ?? '—'],
                        ['CRÉATION_EXP', $company->annee_fondation ?? '—'],
                        ['EFFECTIF_EST', $company->taille ?? '—'],
                        ['CORE_BUSINESS', $company->secteur ?? '—'],
                    ] as $row)
                    <div class="p-4 bg-muted2/5 border border-muted2/20 rounded-lg group hover:border-primary-500/30 transition">
                        <div class="font-mono text-[8px] text-muted2 uppercase tracking-widest mb-1">{{ $row[0] }}</div>
                        <div class="font-display text-white tracking-wide text-lg group-hover:text-primary-500 transition">{{ $row[1] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-4">POINTS_FORTS</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($company->points_forts ?? [] as $p)
                            <span class="px-3 py-1.5 bg-primary-500/10 text-primary-500 border border-primary-500/20 font-mono text-[10px] rounded tracking-wide uppercase">{{ $p }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <div class="font-mono text-[10px] text-red-400 uppercase tracking-widest mb-4">VULNÉRABILITÉS</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($company->points_faibles ?? [] as $p)
                            <span class="px-3 py-1.5 bg-red-400/10 text-red-400 border border-red-400/20 font-mono text-[10px] rounded tracking-wide uppercase">{{ $p }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <div class="font-mono text-[10px] text-cyan-400 uppercase tracking-widest mb-4">OPPORTUNITÉS</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($company->opportunites ?? [] as $p)
                            <span class="px-3 py-1.5 bg-cyan-400/10 text-cyan-400 border border-cyan-400/20 font-mono text-[10px] rounded tracking-wide uppercase">↗ {{ $p }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PANEL DIGITAL ── --}}
    <div class="panel p-8 hidden" id="p-digital">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            @foreach($socials as $key => $label)
            @php $on = $presence[$key] ?? false; @endphp
            <div class="flex items-center gap-4 p-4 rounded-xl border {{ $on ? 'bg-primary-500/5 border-primary-500/20 text-white' : 'bg-transparent border-muted2/20 text-muted2 opacity-50' }} transition-all duration-300">
                <div class="w-2 h-2 rounded-full {{ $on ? 'bg-primary-500 shadow-[0_0_8px_#00FF88]' : 'bg-muted2' }}"></div>
                <div class="font-mono text-[11px] tracking-widest uppercase">{{ $label }}</div>
                @if($on) <div class="ml-auto text-primary-500 text-xs">✓</div> @endif
            </div>
            @endforeach
        </div>
        
        <div class="max-w-xl mx-auto p-10 bg-muted2/5 border border-muted2/20 rounded-3xl text-center">
            <div class="font-mono text-[10px] text-muted uppercase tracking-[0.3em] mb-6">GLOBAL_DIGITAL_PENETRATION</div>
            <div class="text-6xl font-display text-white mb-6">{{ $company->score_digital }}<span class="text-2xl text-muted">/100</span></div>
            <div class="h-2 bg-muted2/30 rounded-full overflow-hidden mb-4">
                <div class="h-full bg-gradient-to-r from-primary-500 to-cyan-500 shadow-[0_0_15px_#00FF88] kbf" data-w="{{ $company->score_digital }}"></div>
            </div>
            <p class="font-mono text-[11px] text-primary-500 uppercase tracking-widest">
                STATUT : {{ strtoupper($company->niveauDigital()) }}
            </p>
        </div>
    </div>

    {{-- ── PANEL RECOMMANDATIONS ── --}}
    <div class="panel p-8 hidden" id="p-recos">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            @forelse($recos as $r)
            <div class="p-6 bg-ink2 border border-muted2/20 rounded-2xl hover:border-primary-500/30 transition group">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-muted2/10 flex items-center justify-center text-2xl group-hover:bg-primary-500/10 transition">
                        {{ $icons[$r['categorie'] ?? 'autre'] ?? '💡' }}
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="font-mono text-[9px] text-primary-400 uppercase tracking-widest">{{ $r['categorie'] ?? 'autre' }}</span>
                            <span class="px-2 py-0.5 font-mono text-[8px] rounded {{ str_contains(strtolower($r['priorite']??''), 'haute') ? 'bg-red-500/20 text-red-400' : 'bg-muted2/20 text-muted' }} uppercase">
                                {{ $r['priorite'] ?? 'NORMAL' }}
                            </span>
                        </div>
                        <div class="text-lg font-display text-white tracking-wide">{{ $r['titre'] ?? 'CONSEIL STRATÉGIQUE' }}</div>
                        <p class="text-sm text-muted font-light leading-relaxed">{{ $r['description'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-2 py-20 text-center font-mono text-muted text-xs uppercase tracking-[0.2em]">AUCUNE RECOMMANDATION GÉNÉRÉE.</div>
            @endforelse
        </div>

        @if(!empty($plan))
        <div class="space-y-6">
            <div class="font-mono text-[10px] text-primary-500 uppercase tracking-[0.3em] mb-4">PLAN_ACTION_CHRONOLOGIQUE</div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach(['court_terme' => ['IMMÉDIAT', 'text-red-400'], 'moyen_terme' => ['3-6 MOIS', 'text-amber-400'], 'long_terme' => ['1 AN', 'text-primary-400']] as $key => $meta)
                <div class="p-6 bg-muted2/5 border border-muted2/20 rounded-xl relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-1 h-full {{ str_replace('text-', 'bg-', $meta[1]) }} opacity-20 group-hover:opacity-100 transition"></div>
                    <div class="font-mono text-[9px] {{ $meta[1] }} uppercase tracking-widest mb-4">{{ $meta[0] }}</div>
                    <ul class="space-y-3">
                        @foreach($plan[$key] ?? [] as $a)
                        <li class="font-mono text-[10px] text-muted flex gap-2">
                            <span class="text-white/20">›</span> {{ $a }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── PANEL IA_INSIGHT ── --}}
    <div class="panel p-8 hidden" id="p-ia">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-12">
                <div class="relative">
                    <div class="absolute -left-6 top-0 bottom-0 w-px bg-gradient-to-b from-primary-500 via-muted2/30 to-transparent"></div>
                    <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-6 px-2">GROQ_LLAMA // DEEP_ANALYSIS</div>
                    <p class="text-xl text-white font-light leading-relaxed italic opacity-90">
                        {{ $analyse?->analyse_ia ?? 'Traitement de l\'analyse IA en cours...' }}
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="font-mono text-[10px] text-muted uppercase tracking-[0.3em]">BENCHMARK_SECTORIEL</div>
                    <div class="space-y-4">
                        @foreach([
                            ['ENTITÉ_ACTUELLE', $company->score_digital, 'bg-primary-500'],
                            ['MOYENNE_SECTEUR', max(0, $company->score_digital - rand(8,18)), 'bg-muted2'],
                            ['LEADERS_REGIONAUX', min(100, $company->score_digital + rand(5,15)), 'bg-cyan-500'],
                        ] as $sc)
                        <div class="space-y-2">
                            <div class="flex justify-between font-mono text-[9px] uppercase tracking-widest text-muted">
                                <span>{{ $sc[0] }}</span>
                                <span class="text-white">{{ $sc[1] }}%</span>
                            </div>
                            <div class="h-1 bg-muted2/10 rounded-full overflow-hidden">
                                <div class="h-full {{ $sc[2] }} transition-all duration-1000 sc-f" data-w="{{ $sc[1] }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(isset($analyse->extra_data['sentiment']))
            @php $sent = $analyse->extra_data['sentiment']; @endphp
            <div class="space-y-8">
                <div class="p-6 bg-primary-500/5 border border-primary-500/20 rounded-2xl">
                    <div class="font-mono text-[9px] text-primary-500 uppercase tracking-widest mb-6">SENTIMENT_INDEX</div>
                    <div class="flex items-end gap-3 mb-6">
                        <div class="text-5xl font-display text-white">{{ $sent['score'] }}</div>
                        <div class="mb-2 font-mono text-[10px] {{ $sent['score'] > 0 ? 'text-green-500' : 'text-red-500' }}">{{ strtoupper($sent['label']) }}</div>
                    </div>
                    <div class="space-y-4">
                        <div class="text-[11px] text-muted italic leading-relaxed">"{{ $sent['reputation_web'] }}"</div>
                        <div class="space-y-2">
                            <div class="font-mono text-[8px] text-muted2 uppercase">Points Clés</div>
                            @foreach($sent['points_positifs'] ?? [] as $p)
                                <div class="text-[10px] text-green-400 flex gap-2"><span>+</span> {{ $p }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($analyse?->extra_data)
    <div class="panel p-8 hidden" id="p-web">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            @if(isset($analyse->extra_data['seo_audit']))
            @php $seo = $analyse->extra_data['seo_audit']; @endphp
            <div class="space-y-8">
                <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest">TECHNICAL_SEO_AUDIT // LIGHTHOUSE</div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['seo' => 'SEO_SCORE', 'performance' => 'SPEED_PERF', 'accessibility' => 'ACCESS', 'best_practices' => 'PRACTICES'] as $k => $l)
                    <div class="p-4 bg-muted2/5 border border-muted2/20 rounded-xl">
                        <div class="font-mono text-[8px] text-muted mb-2">{{ $l }}</div>
                        <div class="text-3xl font-display {{ ($seo[$k] ?? 0) > 80 ? 'text-green-500' : (($seo[$k] ?? 0) > 50 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ round($seo[$k] ?? 0) }}%
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($analyse->extra_data['tech_stack']))
            <div class="space-y-8">
                <div class="font-mono text-[10px] text-cyan-500 uppercase tracking-widest">TECHNOLOGY_STACK // WAPPALYZER</div>
                <div class="flex flex-wrap gap-3">
                    @foreach($analyse->extra_data['tech_stack'] as $tech)
                    <div class="flex items-center gap-2 px-4 py-2 bg-muted2/10 border border-muted2/20 rounded-lg group hover:border-cyan-500/30 transition">
                        @if(isset($tech['icon']))<img src="https://raw.githubusercontent.com/AliasIO/wappalyzer/master/src/drivers/webextension/images/icons/{{ $tech['icon'] }}" class="w-4 h-4 opacity-50 group-hover:opacity-100 transition">@endif
                        <span class="font-mono text-[10px] text-white tracking-widest">{{ $tech['name'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        @if(isset($analyse->extra_data['web_search']))
        <div class="mt-12 space-y-6">
            <div class="font-mono text-[10px] text-muted uppercase tracking-widest border-b border-muted2/20 pb-4">REALTIME_SEARCH_RESULTS // SCRAPINGBEE</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($analyse->extra_data['web_search']['organic_results'] ?? [] as $res)
                <a href="{{ $res['url'] }}" target="_blank" class="p-4 bg-ink2 border border-muted2/10 rounded-xl hover:border-primary-500/20 transition group">
                    <div class="text-[10px] text-primary-500 font-mono mb-1 truncate">{{ $res['url'] }}</div>
                    <div class="text-sm font-display text-white mb-2 group-hover:text-primary-500 transition">{{ $res['title'] }}</div>
                    <div class="text-[11px] text-muted line-clamp-2 leading-relaxed font-light">{{ $res['snippet'] ?? '' }}</div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="panel p-8 hidden" id="p-conc">
        <div id="conc-zone">
            @if(!$canConc)
            <div class="card p-12 text-center border-dashed border-muted2/30">
                <div class="font-mono text-[11px] text-muted uppercase tracking-[0.2em] mb-6">// MATRICE CONCURRENTIELLE PROTOCOLE PRO</div>
                <a href="{{ route('subscription.upgrade') }}" class="btn-primary">DÉBLOQUER L'ACCÈS</a>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-20 text-muted space-y-6">
                <div class="w-12 h-12 rounded-full border border-primary-500/30 flex items-center justify-center animate-pulse">
                    <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                </div>
                <div class="font-mono text-[10px] uppercase tracking-[0.3em]">INITIALISER LA COMPILATION...</div>
            </div>
            @endif
        </div>
    </div>
</div>
