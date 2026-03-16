@extends('layouts.app')

@section('title', 'CORE_ANALYSIS_REPORT // ' . strtoupper($company->nom))

@section('content')
<div class="max-w-5xl mx-auto space-y-12 anim-fade-up" x-data="showPageHandler()">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 border-b border-muted2/30 pb-10">
        <div class="space-y-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2 font-mono text-[9px] text-muted uppercase tracking-[0.2em]">
                    <li><a href="{{ route('analysis.index') }}" class="hover:text-primary-500 transition">ARCHIVES</a></li>
                    <li><span class="text-muted2">/</span></li>
                    <li><a href="{{ route('dashboard') }}" class="hover:text-primary-500 transition">DASHBOARD</a></li>
                    <li><span class="text-muted2">/</span></li>
                    <li class="text-white">NODE_ID:{{ $company->id }}</li>
                </ol>
            </nav>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-primary-500 shadow-[0_0_8px_#00FF88]"></div>
                    <span class="font-mono text-[10px] text-primary-500 tracking-widest uppercase">ANALYSIS_PROTOCOL // COMPLETE</span>
                </div>
                <h1 class="text-5xl font-display text-white tracking-wider">{{ strtoupper($company->nom) }}</h1>
                <p class="font-mono text-[11px] text-muted mt-2 uppercase tracking-widest">
                    {{ $company->secteur ?? 'GENERAL_BUSINESS' }} <span class="mx-2 text-muted2">|</span> 
                    TIMECODE : <span class="text-white">{{ $analyse->created_at->format('d/m/Y') }}</span>
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <button @click="showWhatsApp = !showWhatsApp" class="w-12 h-12 rounded-xl bg-ink2 border border-green-500/30 flex items-center justify-center text-green-500 hover:bg-green-500/10 transition group shadow-lg shadow-green-500/5">
                <svg class="w-6 h-6 group-hover:scale-110 transition duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 0 5.414 0 12.05c0 2.123.552 4.197 1.598 6.004L0 24l6.148-1.613a11.772 11.772 0 005.9 1.564h.005c6.635 0 12.05-5.414 12.05-12.05 0-3.212-1.25-6.231-3.52-8.502z"/></svg>
            </button>
            <a href="{{ route('analysis.pdf', $company->slug) }}" class="btn-primary gap-3 py-3 px-6 text-[11px] tracking-widest font-mono">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                PDF_EXPORT
            </a>
        </div>
    </div>

    {{-- Menu WhatsApp --}}
    <div x-show="showWhatsApp" x-cloak x-transition class="card p-8 border-green-500/20 bg-green-500/[0.02] relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4">
            <button @click="showWhatsApp = false" class="text-muted hover:text-white transition font-mono text-xs">[ESC]</button>
        </div>
        <div class="flex flex-col md:flex-row items-end gap-6">
            <div class="flex-1 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                    <label class="block font-mono text-[10px] text-green-500 uppercase tracking-widest">TRANSMISSION_SECURE_CHANNEL</label>
                </div>
                <input type="text" x-model="whatsappNumber" placeholder="+33 6 00 00 00 00" 
                    class="input-terminal w-full border-green-500/20 focus:border-green-500 focus:shadow-[0_0_15px_rgba(34,197,94,0.1)]">
            </div>
            <button @click="sendWhatsApp" class="w-full md:w-auto h-[52px] px-10 bg-green-500 text-ink font-display text-lg tracking-widest rounded hover:bg-green-400 transition" :disabled="sendingWhatsapp">
                <span x-show="!sendingWhatsapp">ENVOYER_LE_RAPPORT</span>
                <span x-show="sendingWhatsapp">SYNCING...</span>
            </button>
        </div>
    </div>

    {{-- Contenu Principal --}}
    @include('analysis.partials.resultat', ['company' => $company, 'analyse' => $analyse, 'user' => auth()->user()])

    {{-- Concurrents --}}
    <div class="space-y-8" id="concurrents-section">
        <div class="flex items-center gap-4">
            <h2 class="font-display text-2xl text-white tracking-widest uppercase">MARKET_BENCHMARKING</h2>
            <div class="flex-1 h-px bg-muted2/30"></div>
            @if(!$company->competitors->count() && auth()->user()->aAcces('competitors'))
                <button @click="loadCompetitors" class="btn-ghost text-[10px] uppercase tracking-widest px-6 py-2">
                    GÉNÉRER_MATRICE
                </button>
            @endif
        </div>
        
        <div id="competitors-results">
            @if($company->competitors->count())
                @include('analysis.partials.concurrents', ['concurrents' => $company->competitors])
            @else
                <div class="card p-16 text-center border-dashed border-muted2/20">
                    <div class="font-mono text-xs text-muted mb-8 uppercase tracking-[0.2em]">INITIALISER_L'ANALYSE_CONCURRENTIELLE_PROTOCOLE</div>
                    @if(!auth()->user()->aAcces('competitors'))
                        <a href="{{ route('subscription.index') }}" class="btn-ghost">UPGRADER_ACCÈS_PRO</a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Évolution --}}
    @if(auth()->user()->aAcces('history'))
    <div class="space-y-8 pb-20">
        <div class="flex items-center gap-4">
            <h2 class="font-display text-2xl text-white tracking-widest uppercase">PERFORMANCE_EVOLUTION_LOGS</h2>
            <div class="flex-1 h-px bg-muted2/30"></div>
        </div>
        @include('analysis.partials.historique', ['company' => $company])
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function showPageHandler() {
    return {
        showWhatsApp: false,
        whatsappNumber: '{{ auth()->user()->whatsapp_number }}',
        sendingWhatsapp: false,

        sendWhatsApp() {
            if(!this.whatsappNumber){ alert('IDENTIFIANT_REQUIS'); return; }
            this.sendingWhatsapp = true;
            fetch('{{ route("analysis.whatsapp", $company->slug) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ numero: this.whatsappNumber })
            })
            .then(res => res.json())
            .then(data => {
                alert('TRANSFER_OK // ' + data.message);
                this.sendingWhatsapp = false;
                this.showWhatsApp = false;
            })
            .catch(() => {
                this.sendingWhatsapp = false;
                alert('CRITICAL_ERROR // Échec de transmission.');
            });
        },

        loadCompetitors() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerText = 'SCANNING...';

            fetch('{{ route("analysis.concurrents", $company->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('competitors-results').innerHTML = data.html;
                    btn.remove();
                } else {
                    alert('ANALYSIS_FAILURE');
                    btn.disabled = false;
                    btn.innerText = 'GÉNÉRER_MATRICE';
                }
            });
        }
    }
}

// Initializer for results sub-elements if any
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(()=>{
        document.querySelectorAll('.kpi-c').forEach((el,i)=>setTimeout(()=>el.classList.add('lit'),i*80));
        document.querySelectorAll('.kbf').forEach(el=>el.style.width=el.dataset.w+'%');
        document.querySelectorAll('.sc-f').forEach(el=>el.style.width=el.dataset.w+'%');
    },500);
});
</script>
@endpush
