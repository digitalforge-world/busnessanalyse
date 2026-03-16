<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-muted2/30">
                    <th class="px-6 py-5 font-mono text-[9px] text-muted uppercase tracking-[0.2em]">STRUCTURE_NAME</th>
                    <th class="px-6 py-5 font-mono text-[9px] text-muted uppercase tracking-[0.2em]">SECTOR</th>
                    <th class="px-6 py-5 font-mono text-[9px] text-muted uppercase tracking-[0.2em] text-center">DIGITAL_KPI</th>
                    <th class="px-6 py-5 font-mono text-[9px] text-muted uppercase tracking-[0.2em]">ASSETS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-muted2/10">
                @foreach($concurrents as $conc)
                <tr class="hover:bg-white/[0.02] transition group">
                    <td class="px-6 py-5">
                        <div class="text-sm font-display text-white group-hover:text-primary-500 transition tracking-wide">{{ $conc->nom }}</div>
                    </td>
                    <td class="px-6 py-5 text-[11px] font-mono text-muted uppercase tracking-widest">{{ $conc->secteur ?? '—' }}</td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-block px-3 py-1 rounded-[4px] font-mono text-[10px] {{ $conc->score_digital >= 60 ? 'bg-primary-500/10 text-primary-500 border border-primary-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20' }}">
                            {{ $conc->score_digital }}%
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex flex-wrap gap-2">
                            @foreach(($conc->points_forts ?? []) as $pf)
                                <span class="text-[9px] font-mono bg-muted2/20 text-muted px-2 py-0.5 rounded-[3px] border border-muted2/10 uppercase tracking-tighter">{{ $pf }}</span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($concurrents->first()?->analyse_comparative)
    <div class="p-10 bg-primary-500/[0.02] border-t border-muted2/20">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-2 h-px bg-primary-500"></div>
            <div class="font-mono text-[9px] text-primary-500 uppercase tracking-[0.2em]">STRATEGIC_AI_INSIGHT</div>
        </div>
        <p class="text-lg text-white/90 leading-relaxed italic font-light">
            "{{ $concurrents->first()->analyse_comparative }}"
        </p>
    </div>
    @endif
</div>
