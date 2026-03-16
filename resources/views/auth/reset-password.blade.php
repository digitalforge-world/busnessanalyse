<x-guest-layout>
    @section('title', 'Réinitialisation — BIA SYSTEM')

    <div class="login-page">
        <div class="login-split">
            {{-- Partie Gauche : Formulaire --}}
            <div class="login-left">
                <a href="/" class="login-logo">
                    <div class="nav-logo-dot"></div>
                    BIA <span class="opacity-40 font-light">SYSTEM</span>
                </a>

                <div class="login-card">
                    <h1 class="login-title">CRYPTAGE</h1>
                    <p class="login-subtitle">RÉGÉNÉRATION_CLÉ_D'ACCÈS</p>

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">IDENTIFIANT_EMAIL</label>
                            <input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username"
                                class="input-terminal w-full">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <label for="password" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">NOUVEAU_CRYPTAGE</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="input-terminal w-full" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="font-mono text-[10px] text-primary-500 uppercase tracking-widest block">CONFIRMATION_CRYPTAGE</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                class="input-terminal w-full" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-submit">
                                {{ __('RÉINITIALISER_ACCÈS') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Partie Droite : Visuel --}}
            <div class="login-right">
                <div class="login-tagline">
                    <span>SÉCURISEZ</span>
                    <span class="stroke">VOTRE</span>
                    <span class="glow">DATA</span>
                </div>

                <div class="login-stats">
                    <div>
                        <div class="login-stat-n">SHA<span>512</span></div>
                        <div class="login-stat-l">Protocoles de hachage</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
