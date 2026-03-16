@extends('layouts.app')

@section('title', 'REACH_INTELLIGENCE_TERMINAL')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     STYLES COMPLÉMENTAIRES — Spécifiques Recherche
     Consolidés et épurés pour le thème Dark Luxury
     ═══════════════════════════════════════════════════════ --}}
<style>
/* ── SEARCH TERMINAL ── */
.srch-wrap { animation: fade-up 0.7s 0.3s ease both; }
.srch-lbl {
    font-family:'DM Mono',monospace; font-size:10px; color:var(--neon);
    letter-spacing:.14em; margin-bottom:8px;
    display:flex; align-items:center; gap:5px;
}
.srch-lbl::before { content:'>'; animation:blink 1.1s step-end infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

.srch-box {
    display:flex; align-items:center; max-width:700px;
    background:var(--ink2); border:1px solid var(--border); border-radius:4px;
    overflow:hidden; transition:all .3s;
    box-shadow: 0 0 0 0 rgba(0,255,136,0);
}
.srch-box:focus-within {
    border-color:var(--neon);
    box-shadow: 0 0 0 3px rgba(0,255,136,0.1), 0 0 40px rgba(0,255,136,.07);
}
.srch-pre {
    padding: 0 16px; height:56px;
    display:flex; align-items:center;
    font-family:'DM Mono',monospace; font-size:13px; color:var(--neon);
    border-right:1px solid var(--muted2);
    background:rgba(0,255,136,.045);
    white-space:nowrap; user-select:none; letter-spacing:.05em;
}
.srch-inp {
    flex:1; background:transparent; border:none; outline:none;
    color:var(--text); font-size:15px; font-family:'DM Mono',monospace;
    padding:0 18px; height:56px; letter-spacing:.02em; caret-color:var(--neon);
}
.srch-inp::placeholder { color:var(--muted2); }
.srch-btn {
    height:56px; padding:0 32px;
    background:var(--neon); color:var(--ink);
    border:none; font-family:'Bebas Neue',sans-serif; font-size:18px;
    letter-spacing:.1em; cursor:pointer; transition:all .2s;
    display:flex; align-items:center; gap:7px; white-space:nowrap;
    position:relative; overflow:hidden;
}
.srch-btn::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent);
    transform:translateX(-100%); transition:transform .45s;
}
.srch-btn:hover::after { transform:translateX(100%); }
.srch-btn:hover { background:var(--cyan); }
.srch-btn:disabled { background:var(--muted2); cursor:not-allowed; }
.srch-btn:disabled::after { display:none; }

/* Progress bar de recherche */
.prog-wrap {
    max-width:700px; margin-top:14px; padding:14px 18px;
    background:var(--ink2); border:1px solid var(--border); border-radius:4px;
}
.prog-row {
    display:flex; align-items:center; gap:9px;
    font-family:'DM Mono',monospace; font-size:11px; color:var(--muted2);
    padding:4px 0; transition:color .3s; letter-spacing:.04em;
}
.prog-row.on { color:var(--neon); }
.prog-row.ok { color:var(--muted); }
.prog-row.ok::before { content:'✓ '; color:var(--neon); }
.pdot {
    width:6px; height:6px; border-radius:50%; background:var(--muted2);
    flex-shrink:0; transition:all .3s;
}
.prog-row.on .pdot { background:var(--neon); box-shadow:0 0 7px var(--neon); }

/* Animation Entités */
.hero-eye {
    display: inline-flex; align-items: center; gap: 10px;
    font-family:'DM Mono',monospace; font-size:10px; color:var(--neon);
    letter-spacing:.16em; text-transform:uppercase; margin-bottom:28px;
    padding: 6px 14px; border: 1px solid var(--border);
    border-radius: 2px; background: var(--dim);
    animation: fade-up 0.6s ease both;
}
.eye-pulse { width:8px; height:8px; border-radius:50%; background:var(--neon); box-shadow:0 0 8px var(--neon); }

.hero-h1 {
    font-family:'Bebas Neue',sans-serif;
    font-size: clamp(56px, 9vw, 118px);
    line-height: .91; letter-spacing: .02em; color: #fff; margin-bottom: 24px;
    animation: fade-up 0.7s 0.1s ease both;
}
.hero-h1 .stroke { -webkit-text-stroke: 1.5px var(--neon); color: transparent; }
.hero-h1 .glow   { color:var(--neon); text-shadow:0 0 60px rgba(0,255,136,.35); }

.hero-p {
    font-size:16px; color:var(--muted); max-width:540px; line-height:1.75;
    margin-bottom:48px; font-weight:300;
    animation: fade-up 0.7s 0.2s ease both;
}
.hero-p b { color:var(--text); font-weight:400; }

#result-zone {
    position:relative; z-index:10; margin-top: 64px;
}

/* Historique */
.hist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.hist-card {
    background: var(--ink2); border: 1px solid var(--bord2); border-radius: 8px;
    padding: 24px; text-decoration: none; transition: all 0.3s;
    position: relative; overflow: hidden;
}
.hist-card:hover { border-color: var(--border); transform: translateY(-3px); background: var(--ink3); }
.hist-card::before {
    content: ''; position: absolute; left: 0; top: 0; width: 3px; height: 100%;
    background: var(--neon); opacity: 0; transition: opacity 0.3s;
}
.hist-card:hover::before { opacity: 1; }

/* Injected styles from result parts */
.rc { background:var(--ink2); border:1px solid var(--border); border-radius:8px; overflow:hidden; animation:reveal .55s ease both; }
@keyframes reveal{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
</style>

{{-- ═══════ PAGE CONTENT ═══════ --}}
<div class="space-y-16">

    {{-- FEATURES MINI BAR --}}
    <div class="flex flex-wrap gap-8 items-center justify-start border-b border-muted2/20 pb-8 overflow-x-auto no-scrollbar">
        @foreach([['Gemini','Renseignement web'],['Groq','Reasoning Engine'],['PDF','Output Stratégique'],['Concours','Benchmarking']] as $f)
        <div class="flex items-center gap-3 whitespace-nowrap">
            <div class="w-1.5 h-1.5 rounded-full bg-primary-500"></div>
            <div class="font-mono text-[10px] tracking-widest text-muted"><b class="text-white">{{ $f[0] }}</b> · {{ $f[1] }}</div>
        </div>
        @endforeach
    </div>

    {{-- HERO + SEARCH --}}
    <section class="max-w-5xl" x-data="app()" x-cloak>
        <div class="hero-eye mt-4">
            <div class="eye-pulse"></div>Standard Mondial d'Intelligence Commerciale
        </div>

        <h1 class="hero-h1">
            <span>ANALYSEZ</span>
            <span class="stroke">NIMPORTE QUELLE</span>
            <span class="glow">CIBLE BUSINESS</span>
        </h1>

        <p class="hero-p">
            Propulsé par <b>Deep Reasoning Models</b>. Scannez n'importe quelle entité économique mondiale en temps réel pour générer une due diligence complète et des recommandations stratégiques instantanées.
        </p>

        {{-- TERMINAL DE RECHERCHE --}}
        <div class="srch-wrap">
            <div class="srch-lbl uppercase tracking-[0.2em] text-[10px] mb-4">INITIALISER LE SCAN / NOM_ENTREPRISE</div>
            <div class="srch-box">
                <div class="srch-pre font-mono">BIA_QUERY @ login_</div>
                <input class="srch-inp" x-model="q" @keydown.enter="run"
                       placeholder="Ex: Apple Inc, Tesla, LVMH, Samsung..."
                       autocomplete="off" spellcheck="false">
                <button class="srch-btn" @click="run" :disabled="loading||q.trim().length<2">
                    <span x-show="!loading">LANCER LE SCAN</span>
                    <div x-show="loading" class="flex gap-1.5">
                        <span class="w-1 h-1 bg-ink rounded-full animate-bounce"></span>
                        <span class="w-1 h-1 bg-ink rounded-full animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-1 h-1 bg-ink rounded-full animate-bounce [animation-delay:0.4s]"></span>
                    </div>
                </button>
            </div>

            <div x-show="loading" x-transition class="prog-wrap">
                <template x-for="(s,i) in steps" :key="i">
                    <div class="prog-row" :class="{on:i===sa,ok:i<sa}">
                        <div class="pdot"></div>
                        <span x-text="s"></span>
                    </div>
                </template>
            </div>

            <div x-show="err" x-transition class="mt-4 p-4 border border-red-500/30 bg-red-500/5 rounded-lg font-mono text-xs text-red-500" x-text="err"></div>
        </div>
    </section>

    {{-- ZONE RÉSULTATS --}}
    <div id="result-zone"></div>

    {{-- ANALYSES RÉCENTES --}}
    @auth
    @php $hist = auth()->user()->companies()->latest()->take(6)->get(); @endphp
    @if($hist->isNotEmpty())
    <div class="space-y-8 pb-20">
        <div class="flex items-center gap-4">
            <h2 class="font-display text-2xl text-white tracking-widest uppercase">TERMINAUX RÉCENTS</h2>
            <div class="flex-1 h-px bg-muted2/30"></div>
            <a href="{{ route('dashboard') }}" class="font-mono text-[10px] text-muted hover:text-primary-500 transition tracking-[0.2em]">VOIR TOUTE L'ARCHIVE →</a>
        </div>
        <div class="hist-grid">
            @foreach($hist as $c)
            <a href="{{ route('analysis.show',$c->slug) }}" class="hist-card group">
                <div class="flex justify-between items-start mb-4">
                    <div class="text-xl font-display text-white group-hover:text-primary-500 transition">{{ $c->nom }}</div>
                    <div class="font-mono text-[9px] text-muted uppercase tracking-widest">{{ $c->pays ?? 'Global' }}</div>
                </div>
                <div class="font-mono text-[10px] text-muted mb-6 uppercase tracking-widest border-l border-muted2/30 pl-3">{{ $c->secteur ?? 'Secteur non défini' }}</div>
                <div class="flex gap-8">
                    <div>
                        <div class="font-mono text-[8px] text-muted2 uppercase tracking-[0.2em] mb-1">Score Digital</div>
                        <div class="font-display text-2xl text-primary-500">{{ $c->score_digital }}%</div>
                    </div>
                    <div>
                        <div class="font-mono text-[8px] text-muted2 uppercase tracking-[0.2em] mb-1">Croissance</div>
                        <div class="font-display text-2xl text-cyan-500">{{ $c->score_croissance }}%</div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @endauth
</div>

@endsection

@push('scripts')
<script>
/* ── ALPINE APP ── */
function app(){
    return {
        q:'', loading:false, err:null, sa:0, _t:null,
        steps:[
            'AUTH_GOOGLE // Connexion Search Real-time...',
            'CORE_SCAN   // Détection de l\'empreinte digitale...',
            'REASONING   // Analyse du potentiel de croissance...',
            'DELIVERABLE // Compilation du rapport stratégique...',
        ],
        async run(){
            const query=this.q.trim();
            if(this.loading||query.length<2)return;
            this.loading=true; this.err=null; this.sa=0;
            const resZone = document.getElementById('result-zone');
            resZone.innerHTML='';
            clearInterval(this._t);
            this._t=setInterval(()=>{if(this.sa<this.steps.length-1)this.sa++;},1800);
            try{
                const r=await fetch('{{ route("analysis.analyser") }}',{
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,
                        'Accept':'application/json'
                    },
                    body:JSON.stringify({entreprise:query}),
                });
                const d=await r.json();
                clearInterval(this._t);
                if(d.success){
                    resZone.innerHTML=d.html;
                    setTimeout(()=>{
                        document.querySelectorAll('.kpi-c').forEach((el,i)=>setTimeout(()=>el.classList.add('lit'),i*80));
                        document.querySelectorAll('.kbf').forEach(el=>el.style.width=el.dataset.w+'%');
                        document.querySelectorAll('.sc-f').forEach(el=>el.style.width=el.dataset.w+'%');
                        resZone.scrollIntoView({behavior:'smooth',block:'start'});
                    },100);
                } else {
                    this.err=d.upgrade?'PROTOCOLE_BLOQUÉ // Quota atteint. Améliorez votre licence.':d.message||'SÉQUENCE_INTERROMPUE // Erreur système.';
                }
            }catch(e){
                clearInterval(this._t);
                this.err='NETWORK_FAILURE // Connexion perdue avec le noyau IA.';
            }finally{this.loading=false;}
        }
    }
}


/* ── CONCURRENTS ── */
async function loadConc(slug){
    const z=document.getElementById('conc-zone');
    z.innerHTML='<div style="padding:24px;font-family:DM Mono,monospace;font-size:11px;color:var(--muted)">SYSTEM_SEARCH // SCANNING_MARKET_COMPETITORS...</div>';
    const r=await fetch(`/entreprise/${slug}/concurrents`,{
        method:'POST',
        headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
    });
    const d=await r.json();
    if(d.success) z.innerHTML=d.html;
    else if(d.upgrade) z.innerHTML=`<div class="mt-6 p-8 border border-cyan-500/20 bg-cyan-500/5 rounded-xl"><div class="font-mono text-xs text-cyan-400 mb-4 inline-block">// PROTOCOLE_RESTRICITIF : Licence de niveau Starter+ requis</div><br><a href="/subscription" class="btn-primary inline-block">UPGRADER_MAINTENANT</a></div>`;
}

/* ── WHATSAPP ── */
function wa(slug){
    const n=prompt('IDENTIFIANT_WHATSAPP (ex: +33600000000)');
    if(!n)return;
    fetch(`/entreprise/${slug}/whatsapp`,{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
        body:JSON.stringify({numero:n}),
    }).then(r=>r.json()).then(d=>{
        if(d.success)alert('SYNC_SUCCESS // Rapport transmis par canal sécurisé.');
        else if(d.upgrade)alert('ACCESS_DENIED // Fonctionnalité réservée au plan Pro.');
        else alert('SYSTEM_ERROR // Échec de la transmission.');
    });
}
</script>
@endpush
