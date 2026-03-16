@extends('layouts.app')

@section('title', 'SUBSCRIPTION_PLANS')

@section('content')
<div class="max-w-6xl mx-auto space-y-20 py-12">
    {{-- Header --}}
    <div class="text-center space-y-6 anim-fade-up">
        <div class="inline-block px-4 py-1.5 rounded-full bg-primary-500/10 border border-primary-500/20 mb-4">
            <span class="font-mono text-[10px] text-primary-500 uppercase tracking-[0.3em]">CHOOSE_YOUR_LICENSE</span>
        </div>
        <h1 class="text-6xl md:text-7xl font-display text-white tracking-tight">AUDIT_STRATÉGIQUE</h1>
        <p class="text-lg text-muted max-w-2xl mx-auto font-light leading-relaxed italic">
            Activez la puissance totale de l'IA de renseignement d'affaires. Paiement sécurisé par Carte Bancaire (Paiements Mondiaux) ou Mobile Money (Toutes Zones).
        </p>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach(config('plans') as $code => $plan)
            @php $actuel = auth()->user()->plan === $code @endphp
            <div class="card relative flex flex-col transition-all duration-500 group {{ $code === 'pro' ? 'border-primary-500 shadow-[0_0_30px_rgba(0,255,136,0.15)] scale-105 z-10' : 'hover:border-muted2/50' }} p-10 overflow-hidden">
                
                @if($code === 'pro')
                    <div class="absolute top-0 right-0 py-1.5 px-6 bg-primary-500 text-ink font-mono text-[9px] font-bold uppercase tracking-widest rounded-bl-xl shadow-lg">
                        RECOMMENDED
                    </div>
                @endif

                {{-- Plan Identity --}}
                <div class="mb-10 text-center">
                    <h3 class="font-mono text-[11px] text-muted-foreground uppercase tracking-[0.4em] mb-4 group-hover:text-primary-500 transition-colors">{{ $plan['label'] }}</h3>
                    <div class="flex items-center justify-center gap-1">
                        <span class="text-xs font-mono text-muted group-hover:text-white">$</span>
                        <span class="text-5xl font-display text-white tracking-tighter">{{ $plan['price_usd'] }}</span>
                        <span class="text-xs font-mono text-muted">/MO</span>
                    </div>
                </div>

                {{-- Features --}}
                <ul class="space-y-5 mb-12 flex-1 relative">
                    <li class="flex items-start gap-4">
                        <span class="text-primary-500 font-mono text-sm">›</span>
                        <span class="text-[13px] font-mono text-white leading-snug tracking-tight">
                            {{ $plan['analyses_limit'] === -1 ? 'Analyses_illimitées' : $plan['analyses_limit'] . '_analyses_/_mois' }}
                        </span>
                    </li>
                    @foreach(['pdf_export' => 'Rapports_PDF', 'competitors' => 'Veille_Concurrents', 'whatsapp' => 'Canal_WhatsApp', 'history' => 'Logs_Evolution'] as $key => $label)
                        <li class="flex items-start gap-4 {{ $plan[$key] ? '' : 'opacity-20 grayscale' }}">
                            <span class="{{ $plan[$key] ? 'text-primary-500' : 'text-muted2' }} font-mono text-sm">{{ $plan[$key] ? '✓' : '✗' }}</span>
                            <span class="text-[12px] font-mono text-muted uppercase tracking-wider leading-snug">{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>

                {{-- Action Buttons --}}
                <div class="space-y-3 relative">
                    @if($actuel)
                        <div class="w-full py-4 text-center font-mono text-[10px] text-primary-500 border border-primary-500/20 bg-primary-500/5 uppercase tracking-[0.2em] rounded-xl">
                            CURRENT_PLAN
                        </div>
                    @elseif($plan['price_usd'] > 0)
                        <form method="POST" action="{{ route('subscription.stripe') }}">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $code }}">
                            <button type="submit" class="btn-ghost w-full py-4 text-[10px] tracking-[0.2em] border-muted2/30 hover:border-white">
                                CREDIT_CARD
                            </button>
                        </form>
                        <form method="POST" action="{{ route('subscription.cinetpay') }}">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $code }}">
                            <button type="submit" class="btn-primary w-full py-4 text-[10px] tracking-[0.2em]">
                                MOBILE_MONEY
                            </button>
                        </form>
                    @else
                        <div class="w-full py-4 text-center font-mono text-[10px] text-muted border border-muted2/20 bg-muted2/5 uppercase tracking-[0.2em] rounded-xl">
                            DEFAULT_ACCESS
                        </div>
                    @endif
                </div>

                {{-- Decoration background --}}
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary-500/5 rounded-full blur-3xl group-hover:bg-primary-500/10 transition-all duration-700"></div>
            </div>
        @endforeach
    </div>

    {{-- Legal/Info Footer --}}
    <div class="card p-10 max-w-4xl mx-auto overflow-hidden relative">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <div class="w-16 h-16 rounded-2xl bg-muted2/5 border border-muted2/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-primary-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div class="space-y-3">
                <div class="font-mono text-[9px] text-muted uppercase tracking-[0.3em]">PAYMENT_SECURITY_PROTOCOL</div>
                <p class="text-sm text-muted font-light leading-relaxed">
                    Les transactions par carte sont opérées par <strong class="text-white">Stripe</strong> (Protocole SSL 256 bits). 
                    Le règlement Mobile Money (TMoney, Flooz, Wave, Orange, MTN) est sécurisé par <strong class="text-white">CinetPay</strong>. 
                    Activation immédiate après validation du paiement.
                </p>
            </div>
        </div>
        {{-- Scanner line animation effect --}}
        <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-primary-500/30 to-transparent animate-scan-slow opacity-20"></div>
    </div>
</div>

<style>
@keyframes scan-slow {
    0% { top: 0; }
    100% { top: 100%; }
}
.animate-scan-slow {
    animation: scan-slow 10s linear infinite;
    position: absolute;
}
</style>
@endsection
