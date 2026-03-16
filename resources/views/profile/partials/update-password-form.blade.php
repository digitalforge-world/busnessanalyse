<section>
    <header class="space-y-2 mb-8">
        <h2 class="text-xl font-display text-white tracking-wide uppercase">
            {{ __('CHANGEMENT_CRYPTAGE') }}
        </h2>

        <p class="text-sm text-muted font-light">
            {{ __('Assurez une sécurité maximale en utilisant des protocoles de mot de passe complexes.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-8">
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-input-label for="update_password_current_password" :value="__('MOT_DE_PASSE_ACTUEL')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password" :value="__('NOUVEAU_CRYPTAGE')" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password_confirmation" :value="__('CONFIRMER_CRYPTAGE')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-6 pt-4">
            <x-primary-button>{{ __('METTRE_À_JOUR_SÉCURITÉ') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="font-mono text-[10px] text-primary-500 uppercase tracking-widest"
                >{{ __('CRYPTAGE_RECONFIGURÉ.') }}</p>
            @endif
        </div>
    </form>
</section>
