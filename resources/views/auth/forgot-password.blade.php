<x-guest-layout>
    @section('title', 'Récupération — BIA SYSTEM')

    <div class="login-page">
        <div class="login-split">
            {{-- Partie Gauche : Formulaire --}}
            <div class="login-left">
                <a href="/" class="login-logo">
                    <div class="nav-logo-dot"></div>
                    BIA <span class="opacity-40 font-light">SYSTEM</span>
                </a>

                <div class="login-card">
                    <h1 class="login-title">RÉCUPÉRATION</h1>
                    <p class="login-subtitle">RECOUVREMENT_ACCÈS_SYSTÈME</p>

                    <div class="mb-8 text-sm text-muted font-light leading-relaxed">
                        {{ __('Entrez votre identifiant (email) pour recevoir un protocole de réinitialisation de votre clé d\'accès.') }}
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">IDENTIFIANT_EMAIL</label>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                class="input-terminal w-full" placeholder="nom@exemple.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-submit">
                                {{ __('ENVOYER_LIEN_RÉINITIALISATION') }}
                            </button>
                        </div>

                        <div class="login-register-link">
                            <a href="{{ route('login') }}">[RETOUR_CONNEXION]</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Partie Droite : Visuel --}}
            <div class="login-right">
                <div class="login-tagline">
                    <span>RESTAUREZ</span>
                    <span class="stroke">VOTRE</span>
                    <span class="glow">ACCÈS</span>
                </div>

                <div class="login-stats">
                    <div>
                        <div class="login-stat-n">256<span>BIT</span></div>
                        <div class="login-stat-l">Cryptage militaire</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
