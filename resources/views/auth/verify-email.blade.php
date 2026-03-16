<x-guest-layout>
    @section('title', 'Validation — BIA SYSTEM')

    <div class="register-page">
        <div class="register-card">
            <div class="mb-8">
                <a href="/" class="login-logo">
                    <div class="nav-logo-dot"></div>
                    BIA <span class="opacity-40 font-light">SYSTEM</span>
                </a>
                <h1 class="login-title">VALIDATION</h1>
                <p class="login-subtitle">VÉRIFICATION_IDENTITÉ_NUMÉRIQUE</p>
            </div>

            <div class="mb-8 text-sm text-muted font-light leading-relaxed">
                {{ __('Merci pour votre inscription ! Avant de commencer, veuillez valider votre identité en cliquant sur le lien que nous venons de vous envoyer par email. Si vous n\'avez rien reçu, nous pouvons vous renvoyer un protocole.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-8 font-mono text-[10px] text-primary-500 uppercase tracking-widest bg-primary-500/5 p-4 border border-primary-500/20 rounded">
                    {{ __('UN_NOUVEAU_PROTOCOLE_A_ÉTÉ_DÉPLOYÉ_DANS_VOTRE_BOITE_MAIL.') }}
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-6">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-submit w-full">
                        {{ __('RENVOYER_LE_PROTOCOLE') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="font-mono text-[10px] text-muted hover:text-white transition uppercase tracking-widest underline decoration-muted2 underline-offset-4">
                        {{ __('DÉCONNEXION_SESSION') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
