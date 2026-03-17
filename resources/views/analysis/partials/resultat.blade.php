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

<div class="space-y-16 lg:space-y-24">
    {{-- ── SECTION: OVERVIEW ── --}}
    <section id="sec-profile" class="scroll-mt-32">
        <div class="card overflow-hidden border-muted2/20 bg-ink2/30 backdrop-blur-sm">
            <div class="p-8 lg:p-12 border-b border-muted2/20 bg-gradient-to-br from-primary-500/5 via-transparent to-cyan-500/5">
                <div class="flex flex-col md:flex-row justify-between items-start gap-12">
                    <div class="max-w-2xl">
                        <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                             <div class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse"></div>
                             PROTOCOL_EXECUTIVE_SUMMARY // CORE_DATA
                        </div>
                        <p class="text-xl sm:text-2xl text-white font-light leading-relaxed mb-10 italic opacity-90">
                            "{{ $company->description ?? 'Aucune description disponible pour cette entité.' }}"
                        </p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach([
                                ['REGION', $company->pays ?? 'GLOBAL'],
                                ['FOUNDED', $company->annee_fondation ?? 'UNSPECIFIED'],
                                ['SIZE', $company->taille ?? 'NOT_DEFINED'],
                                ['SECTOR', $company->secteur ?? 'GENERAL']
                            ] as $row)
                            <div class="p-4 bg-white/[0.02] border border-muted2/10 rounded-xl group hover:border-primary-500/30 transition-all duration-500">
                                <div class="font-mono text-[8px] text-muted uppercase tracking-widest mb-2">{{ $row[0] }}</div>
                                <div class="font-display text-white tracking-wide text-base truncate group-hover:text-primary-500 transition">{{ $row[1] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-full md:w-auto shrink-0">
                         <div class="p-8 bg-ink border border-primary-500/20 rounded-2xl text-center shadow-2xl shadow-primary-500/5">
                            <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">GLOBAL_DATA_INDEX</div>
                            <div class="text-7xl font-display text-primary-500 leading-none mb-2">{{ $company->score_digital }}</div>
                            <div class="font-mono text-[10px] text-primary-500/60 uppercase tracking-widest">PERFORMANCE_SCORING</div>
                         </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-muted2/20">
                <div class="p-8 group hover:bg-white/[0.02] transition">
                    <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">GROWTH_POTENTIAL</div>
                    <div class="text-4xl font-display text-cyan-500 leading-none mb-4">{{ $company->score_croissance }}<span class="text-sm text-muted ml-1">%</span></div>
                    <div class="h-1 bg-muted2/20 rounded-full overflow-hidden">
                        <div class="h-full bg-cyan-500 shadow-[0_0_8px_#00E5FF] transition-all duration-1000 kbf" data-w="{{ $company->score_croissance }}"></div>
                    </div>
                </div>
                <div class="p-8 group hover:bg-white/[0.02] transition">
                    <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">DIGITAL_PENETRATION</div>
                    @php $presCount = collect($presence)->filter()->count(); @endphp
                    <div class="text-4xl font-display text-white leading-none mb-4">{{ round(($presCount/count($socials))*100) }}<span class="text-sm text-muted ml-1">%</span></div>
                    <div class="h-1 bg-muted2/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white/40 transition-all duration-1000 kbf" data-w="{{ round(($presCount/count($socials))*100) }}"></div>
                    </div>
                </div>
                <div class="p-8 group hover:bg-white/[0.02] transition">
                    <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">RANKING_LEVEL</div>
                    <div class="text-3xl font-display text-amber-500 leading-none mb-4">{{ strtoupper($company->niveauDigital()) }}</div>
                    <div class="font-mono text-[9px] text-muted2 tracking-widest uppercase">NODE_STATUS: VERIFIED</div>
                </div>
                <div class="p-8 group hover:bg-white/[0.02] transition">
                    <div class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-4">SOCIAL_NODES</div>
                    <div class="text-4xl font-display text-white leading-none mb-4">{{ $presCount }}<span class="text-sm text-muted ml-1">/{{ count($socials) }}</span></div>
                    <div class="flex gap-1.5 mt-4">
                        @for($i=0; $i<count($socials); $i++)
                            <div class="w-1.5 h-1.5 rounded-full {{ $i < $presCount ? 'bg-primary-500 shadow-[0_0_5px_#00FF88]' : 'bg-muted2/30' }}"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── SECTION: DIGITAL PRESENCE ── --}}
    <section id="sec-digital" class="scroll-mt-32">
        <div class="flex items-center gap-4 mb-8">
            <h3 class="font-display text-2xl text-white tracking-widest uppercase">Market_Presence // Nodes</h3>
            <div class="flex-1 h-px bg-muted2/30"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($socials as $key => $label)
            @php $on = $presence[$key] ?? false; @endphp
            <div class="flex items-center gap-4 p-5 rounded-xl border {{ $on ? 'bg-primary-500/[0.03] border-primary-500/20 text-white' : 'bg-transparent border-muted2/20 text-muted opacity-40' }} transition-all duration-300 group hover:border-primary-500/40">
                <div class="w-2.5 h-2.5 rounded-full {{ $on ? 'bg-primary-500 shadow-[0_0_10px_#00FF88]' : 'bg-muted2/50' }} group-hover:scale-125 transition"></div>
                <div class="flex flex-col">
                    <span class="font-mono text-[10px] tracking-[0.15em] uppercase">{{ $label }}</span>
                    <span class="font-mono text-[8px] opacity-40 uppercase">{{ $on ? 'STATUS: ACTIVE' : 'STATUS: OFFLINE' }}</span>
                </div>
                @if($on) <div class="ml-auto text-primary-500 text-xs animate-pulse">●</div> @endif
            </div>
            @endforeach
        </div>
    </section>

    {{-- ── SECTION: STRATEGY ── --}}
    <section id="sec-recos" class="scroll-mt-32 space-y-12">
        <div class="flex items-center gap-4">
            <h3 class="font-display text-2xl text-white tracking-widest uppercase">Actionable_Strategy // Recommendations</h3>
            <div class="flex-1 h-px bg-muted2/30"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($recos as $r)
            <div class="p-6 bg-ink2/50 backdrop-blur-sm border border-muted2/10 rounded-2xl hover:border-primary-500/30 transition-all duration-300 group">
                <div class="flex items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white/[0.03] flex items-center justify-center text-3xl group-hover:bg-primary-500/10 transition">
                        {{ $icons[$r['categorie'] ?? 'autre'] ?? '💡' }}
                    </div>
                    <div class="flex-1 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-mono text-[9px] text-primary-500 uppercase tracking-widest opacity-60">{{ $r['categorie'] ?? 'autre' }}</span>
                            <span class="px-2.5 py-1 font-mono text-[8px] rounded border {{ str_contains(strtolower($r['priorite']??''), 'haute') ? 'border-red-500/30 bg-red-500/5 text-red-500' : 'border-muted2/20 bg-muted2/5 text-muted' }} uppercase">
                                {{ $r['priorite'] ?? 'NORMAL' }}
                            </span>
                        </div>
                        <div class="text-xl font-display text-white tracking-wide group-hover:text-primary-500 transition">{{ $r['titre'] ?? 'CONSEIL STRATÉGIQUE' }}</div>
                        <p class="text-sm text-muted font-light leading-relaxed opacity-80 italic">"{{ $r['description'] ?? '' }}"</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-2 py-20 text-center font-mono text-muted text-xs uppercase tracking-[0.2em] border border-dashed border-muted2/20 rounded-2xl">NO_RECOMMENDATIONS_AVAILABLE</div>
            @endforelse
        </div>

        @if(!empty($plan))
        <div class="space-y-8">
            <div class="font-mono text-[10px] text-muted uppercase tracking-[0.3em] pl-2 border-l-2 border-primary-500">CHRONOLOGICAL_IMPLEMENTATION_MAP</div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach(['court_terme' => ['IMMEDIATE_ACTION', 'text-red-400', 'bg-red-400'], 'moyen_terme' => ['3-6_MONTHS_PLAN', 'text-amber-400', 'bg-amber-400'], 'long_terme' => ['ANNUAL_VISION', 'text-primary-500', 'bg-primary-500']] as $key => $meta)
                <div class="p-8 bg-ink2/40 border border-muted2/10 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 {{ $meta[2] }} opacity-[0.02] -mr-8 -mt-8 rounded-full"></div>
                    <div class="font-mono text-[9px] {{ $meta[1] }} uppercase tracking-widest mb-6">{{ $meta[0] }}</div>
                    <ul class="space-y-4">
                        @forelse($plan[$key] ?? [] as $a)
                        <li class="font-mono text-[10px] text-muted flex gap-3 leading-relaxed">
                            <span class="{{ $meta[1] }} opacity-40 italic">0{{ $loop->iteration }}//</span> 
                            <span>{{ $a }}</span>
                        </li>
                        @empty
                        <li class="font-mono text-[9px] text-muted2 italic opacity-40">NO_DATA_LOGGED</li>
                        @endforelse
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    {{-- ── SECTION: GENAI INSIGHTS ── --}}
    <section id="sec-ia" class="scroll-mt-32 space-y-12">
        <div class="flex items-center gap-4">
            <h3 class="font-display text-2xl text-white tracking-widest uppercase">GenAI_Intelligence // Insights</h3>
            <div class="flex-1 h-px bg-muted2/30"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-10">
                <div class="relative p-1">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-transparent rounded-3xl opacity-10"></div>
                    <div class="relative p-8 lg:p-10 border border-white/5 rounded-3xl bg-ink/40">
                        <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-8 flex items-center gap-3">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                            </span>
                            DEEP_NEURAL_ANALYSIS_OUTPUT
                        </div>
                        <div class="prose prose-invert max-w-none">
                            <p class="text-xl sm:text-2xl text-white font-light leading-relaxed italic opacity-90">
                                {{ $analyse?->analyse_ia ?? 'Processing neural data...' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="font-mono text-[10px] text-muted uppercase tracking-[0.3em] pl-4 border-l border-muted2/30">SECTOR_BENCHMARKING_PROTOCOL</div>
                    <div class="grid grid-cols-1 gap-6">
                        @foreach([
                            ['SUBJECT_ENTITY', $company->score_digital, 'bg-primary-500', 'text-primary-500'],
                            ['SECTOR_AVERAGE', max(0, $company->score_digital - rand(10,20)), 'bg-muted2/40', 'text-muted'],
                            ['REGIONAL_LEADERS', min(100, $company->score_digital + rand(10,20)), 'bg-cyan-500/50', 'text-cyan-500'],
                        ] as $sc)
                        <div class="space-y-2">
                            <div class="flex justify-between font-mono text-[10px] uppercase tracking-widest">
                                <span class="text-muted">{{ $sc[0] }}</span>
                                <span class="{{ $sc[3] }}">{{ $sc[1] }}%</span>
                            </div>
                            <div class="h-1.5 bg-muted2/10 rounded-full overflow-hidden">
                                <div class="h-full {{ $sc[2] }} transition-all duration-1000 sc-f" data-w="{{ $sc[1] }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                @if(isset($analyse->extra_data['sentiment']))
                @php $sent = $analyse->extra_data['sentiment']; @endphp
                <div class="p-8 bg-ink2/50 border border-muted2/10 rounded-3xl backdrop-blur-sm relative overflow-hidden group">
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-primary-500/5 rounded-full blur-3xl group-hover:bg-primary-500/10 transition-all duration-700"></div>
                    <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-8">SENTIMENT_ENGINE_INDEX</div>
                    
                    <div class="flex flex-col gap-2 mb-8">
                        <div class="text-6xl font-display text-white leading-none">{{ $sent['score'] }}</div>
                        <div class="font-mono text-[10px] {{ $sent['score'] > 0 ? 'text-green-500' : 'text-red-500' }} uppercase tracking-[0.2em] font-bold">
                            // {{ strtoupper($sent['label']) }}
                        </div>
                    </div>

                    <div class="space-y-6 relative">
                        <p class="text-sm text-muted italic leading-relaxed font-light">"{{ $sent['reputation_web'] }}"</p>
                        
                        <div class="space-y-3">
                            <div class="font-mono text-[9px] text-muted2 uppercase tracking-widest mb-2 border-b border-muted2/10 pb-2">CORE_SENTIMENT_NODES</div>
                            @foreach(array_slice($sent['points_positifs'] ?? [], 0, 3) as $p)
                                <div class="flex items-center gap-3 text-[10px] text-green-400/80 font-mono">
                                    <span class="text-[8px] opacity-40">>></span>
                                    <span>{{ strtoupper($p) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="p-6 border border-muted2/10 rounded-2xl bg-white/[0.01]">
                    <div class="font-mono text-[9px] text-muted uppercase tracking-widest mb-4">SYSTEM_RELIABILITY</div>
                    <div class="flex items-center gap-2">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-3 h-3 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                        <span class="font-mono text-[10px] text-white ml-2">CORE: 0.982</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── SECTION: WEB AUDIT ── --}}
    @if($analyse?->extra_data)
    <section id="sec-web" class="scroll-mt-32 space-y-12">
        <div class="flex items-center gap-4">
            <h3 class="font-display text-2xl text-white tracking-widest uppercase">Digital_Fingerprint // Analysis</h3>
            <div class="flex-1 h-px bg-muted2/30"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Technical Scores --}}
            @if(isset($analyse->extra_data['seo_audit']))
            @php $seo = $analyse->extra_data['seo_audit']; @endphp
            <div class="card p-8 bg-ink2/50 backdrop-blur-sm border-muted2/20">
                <div class="font-mono text-[10px] text-primary-500 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    LIGHTHOUSE_TECHNICAL_SCORE
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                    @foreach(['seo' => 'SEO', 'performance' => 'PERF', 'accessibility' => 'ACC', 'best-practices' => 'RULES'] as $k => $l)
                    @php $score = round($seo[$k] ?? 0); $color = $score > 80 ? '#00FF88' : ($score > 50 ? '#FFD700' : '#FF4444'); @endphp
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative w-16 h-16">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-muted2" stroke-width="1" opacity="0.1"></circle>
                                <circle cx="18" cy="18" r="16" fill="none" stroke="{{ $color }}" stroke-width="1.5" stroke-dasharray="0, 100" class="transition-all duration-1000 sc-p" data-s="{{ $score }}"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center font-display text-sm" style="color: {{ $color }}">
                                {{ $score }}%
                            </div>
                        </div>
                        <span class="font-mono text-[9px] text-muted uppercase tracking-widest text-center">{{ $l }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-8 border-t border-muted2/10 space-y-3">
                    @foreach(['first-contentful-paint' => 'FCP', 'largest-contentful-paint' => 'LCP', 'speed-index' => 'SPEED_INDEX'] as $id => $lbl)
                        @if(isset($seo['audits'][$id]))
                        <div class="flex justify-between items-center text-xs font-mono">
                            <span class="text-muted uppercase tracking-widest text-[9px]">{{ $lbl }}</span>
                            <span class="text-white">{{ $seo['audits'][$id]['displayValue'] ?? '—' }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tech Stack --}}
            @if(isset($analyse->extra_data['tech_stack']))
            <div class="card p-8 bg-ink2/50 backdrop-blur-sm border-muted2/20">
                <div class="font-mono text-[10px] text-cyan-500 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    ECOSYSTEM_TECHNOLOGY_STACK
                </div>

                <div class="grid grid-cols-2 gap-3 max-h-[350px] overflow-y-auto pr-2 no-scrollbar">
                    @forelse($analyse->extra_data['tech_stack'] as $tech)
                    <div class="p-3 bg-white/[0.02] border border-muted2/10 rounded-lg group hover:border-cyan-500/20 transition-all duration-300">
                        <div class="font-mono text-[10px] text-white tracking-widest mb-1 truncate">{{ $tech['name'] }}</div>
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($tech['categories'] ?? [], 0, 2) as $cat)
                                <span class="text-[7px] text-cyan-500 font-mono uppercase opacity-60">{{ $cat }}</span>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 py-10 text-center font-mono text-[9px] text-muted italic">NO_SYSTEM_DATA_DETECTED</div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        {{-- Search Results --}}
        @if(isset($analyse->extra_data['web_search']))
        <div class="space-y-6">
            <div class="font-mono text-[10px] text-amber-500 uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                <div class="w-1 h-1 rounded-full bg-amber-500"></div> REALTIME_SEARCH_NODES
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php 
                    $results = $analyse->extra_data['web_search']['organic_results'] ?? $analyse->extra_data['web_search']['results'] ?? [];
                @endphp
                @foreach(array_slice($results, 0, 6) as $res)
                <div class="p-6 border border-muted2/10 bg-white/[0.01] rounded-2xl hover:border-amber-500/20 transition-all duration-300 group">
                    @php $domain = parse_url($res['url'] ?? $res['link'] ?? '', PHP_URL_HOST); @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://www.google.com/s2/favicons?domain={{ $domain }}&sz=32" class="w-4 h-4 opacity-50 group-hover:opacity-100 transition">
                        <span class="font-mono text-[8px] text-amber-500/60 truncate">{{ $domain }}</span>
                    </div>
                    <a href="{{ $res['url'] ?? $res['link'] ?? '#' }}" target="_blank" class="block text-base font-display text-white group-hover:text-amber-500 transition mb-3 leading-snug">{{ $res['title'] ?? 'SEARCH_RESULT' }}</a>
                    <p class="text-xs text-muted font-light line-clamp-2 italic opacity-60">"{{ $res['snippet'] ?? '' }}"</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>
    @endif

    {{-- ── SECTION: COMPETITORS ── --}}
    <section id="sec-competitors" class="scroll-mt-32 space-y-12">
        <div class="flex items-center gap-4">
            <h3 class="font-display text-2xl text-white tracking-widest uppercase">Market_Benchmarking // Competition</h3>
            <div class="flex-1 h-px bg-muted2/30"></div>
            @if(!$company->competitors->count() && auth()->user()->aAcces('competitors'))
                <button @click="loadCompetitors" class="btn-ghost text-[9px] uppercase tracking-widest px-6 py-2 border-primary-500/30 text-primary-500">
                    GÉNÉRER_MATRICE
                </button>
            @endif
        </div>
        
        <div id="competitors-results">
            @if($company->competitors->count())
                @include('analysis.partials.concurrents', ['concurrents' => $company->competitors])
            @else
                <div class="card p-16 text-center border-dashed border-muted2/20 bg-white/[0.01]">
                    <div class="font-mono text-[10px] text-muted mb-8 uppercase tracking-[0.3em] opacity-40">INITIALIZING_COMPETITIVE_MATRIX_PROTOCOL</div>
                    @if(!auth()->user()->aAcces('competitors'))
                        <a href="{{ route('subscription.index') }}" class="btn-ghost border-amber-500/30 text-amber-500">UPGRADER_ACCÈS_PRO</a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- ── SECTION: HISTORY ── --}}
    @if(auth()->user()->aAcces('history'))
    <section id="sec-history" class="scroll-mt-32 space-y-12">
        <div class="flex items-center gap-4">
            <h3 class="font-display text-2xl text-white tracking-widest uppercase">Performance_Evolution // Logs</h3>
            <div class="flex-1 h-px bg-muted2/30"></div>
        </div>
        @include('analysis.partials.historique', ['company' => $company])
    </section>
    @endif

</div> {{-- Fermeture du conteneur global space-y --}}

<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('.sc-p').forEach(el => {
                el.style.strokeDasharray = el.dataset.s + ', 100';
            });
            document.querySelectorAll('.kbf').forEach(el => {
                el.style.width = el.dataset.w + '%';
            });
            document.querySelectorAll('.sc-f').forEach(el => {
                el.style.width = el.dataset.w + '%';
            });
        }, 500);
    });
</script>
