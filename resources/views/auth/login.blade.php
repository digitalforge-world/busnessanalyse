<x-guest-layout>
    @section('title', 'Connexion — BIA SYSTEM')

    <div class="login-page">
        <div class="login-split">
            {{-- Partie Gauche : Formulaire --}}
            <div class="login-left">
                <a href="/" class="login-logo">
                    <div class="nav-logo-dot"></div>
                    BIA <span class="opacity-40 font-light">SYSTEM</span>
                </a>

                <div class="login-card">
                    <h1 class="login-title">IDENTIFICATION</h1>
                    <p class="login-subtitle">ACCÈS AU TERMINAL D'ANALYSE</p>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">IDENTIFIANT / EMAIL</label>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                                class="input-terminal w-full" placeholder="nom@exemple.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label for="password" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">CLÉ D'ACCÈS / PASSWORD</label>
                                @if (Route::has('password.request'))
                                    <a class="font-mono text-[9px] text-muted hover:text-white transition uppercase tracking-widest" href="{{ route('password.request') }}">
                                        [OUBLIÉ ?]
                                    </a>
                                @endif
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="input-terminal w-full" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center gap-3">
                            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 bg-ink border border-muted2 rounded checked:bg-primary-500 transition cursor-pointer">
                            <label for="remember_me" class="font-mono text-[10px] text-muted uppercase tracking-widest cursor-pointer select-none">Rester connecté</label>
                        </div>

                        <button type="submit" class="btn-submit">
                            SE CONNECTER
                        </button>

                        <div class="login-register-link">
                            PAS ENCORE DE COMPTE ? <a href="{{ route('register') }}">[CRÉER UN ACCÈS]</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Partie Droite : Visuel & Stats --}}
            <div class="login-right">
                <div class="login-tagline">
                    <span>DÉPORTEZ</span>
                    <span class="stroke">VOTRE</span>
                    <span class="glow">VISION</span>
                </div>

                <div class="login-stats">
                    <div>
                        <div class="login-stat-n"><span>+</span>250</div>
                        <div class="login-stat-l">Sources de données</div>
                    </div>
                    <div>
                        <div class="login-stat-n">99<span>%</span></div>
                        <div class="login-stat-l">Précision IA</div>
                    </div>
                    <div>
                        <div class="login-stat-n"><span><</span>10s</div>
                        <div class="login-stat-l">Temps d'analyse</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
