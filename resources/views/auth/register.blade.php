<x-guest-layout>
    @section('title', 'Inscription — BIA SYSTEM')

    <div class="login-page">
        <div class="login-split">
            {{-- Partie Gauche : Formulaire --}}
            <div class="login-left">
                <a href="/" class="login-logo">
                    <div class="nav-logo-dot"></div>
                    BIA <span class="opacity-40 font-light">SYSTEM</span>
                </a>

                <div class="login-card">
                    <h1 class="login-title">CRÉATION D'ACCÈS</h1>
                    <p class="login-subtitle">IDENTIFICATION NOUVEL UTILISATEUR</p>

                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <div class="register-grid-2">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label for="name" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">NOM COMPLET</label>
                                <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                                    class="input-terminal w-full" placeholder="John Doe">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">ADRESSE EMAIL</label>
                                <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                                    class="input-terminal w-full" placeholder="nom@exemple.com">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="register-grid-2">
                            <!-- Password -->
                            <div class="space-y-2">
                                <label for="password" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">MOT DE PASSE</label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="input-terminal w-full" placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2">
                                <label for="password_confirmation" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">CONFIRMATION</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                    class="input-terminal w-full" placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-submit">
                                CRÉER LE COMPTE
                            </button>
                        </div>

                        <div class="login-register-link">
                            DÉJÀ INSCRIT ? <a href="{{ route('login') }}">[SE CONNECTER]</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Partie Droite : Visuel & Stats --}}
            <div class="login-right">
                <div class="login-tagline">
                    <span>FORGEZ</span>
                    <span class="stroke">VOTRE</span>
                    <span class="glow">AVENIR</span>
                </div>

                <div class="login-stats">
                    <div>
                        <div class="login-stat-n"><span>+</span>1.2K</div>
                        <div class="login-stat-l">Utilisateurs actifs</div>
                    </div>
                    <div>
                        <div class="login-stat-n">98<span>%</span></div>
                        <div class="login-stat-l">Satisfaction client</div>
                    </div>
                    <div>
                        <div class="login-stat-n">24<span>/7</span></div>
                        <div class="login-stat-l">Support technique</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
