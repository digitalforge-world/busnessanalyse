<x-guest-layout>
    @section('title', 'Confirmation — BIA SYSTEM')

    <div class="register-page">
        <div class="register-card">
            <div class="mb-8">
                <a href="/" class="login-logo">
                    <div class="nav-logo-dot"></div>
                    BIA <span class="opacity-40 font-light">SYSTEM</span>
                </a>
                <h1 class="login-title">SÉCURITÉ</h1>
                <p class="login-subtitle">CONFIRMATION_ZONE_CRITIQUE</p>
            </div>

            <div class="mb-8 text-sm text-muted font-light leading-relaxed">
                {{ __('Il s\'agit d\'une zone sécurisée. Veuillez confirmer votre clé d\'accès avant de continuer.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <!-- Password -->
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('CLÉ_D\'ACCÈS')" />
                    <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-submit w-full">
                        {{ __('CONFIRMER_ACCÈS') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
