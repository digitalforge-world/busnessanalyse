<section>
    <header class="space-y-2 mb-8">
        <h2 class="text-xl font-display text-white tracking-wide uppercase">
            {{ __('INFOS_IDENTITÉ') }}
        </h2>

        <p class="text-sm text-muted font-light">
            {{ __("Mettez à jour vos informations système et votre adresse de synchronisation.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <x-input-label for="name" :value="__('NOM_UTILISATEUR')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="email" :value="__('EMAIL_SYNCHRONISATION')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="p-4 bg-amber-500/5 border border-amber-500/20 rounded-xl mt-4">
                    <p class="text-xs font-mono text-amber-500 leading-relaxed uppercase tracking-widest">
                        {{ __('CRITICAL: ADRESSE_NON_VÉRIFIÉE') }}
                    </p>
                    
                    <button form="send-verification" class="mt-2 text-[10px] font-mono text-white hover:text-primary-500 transition underline tracking-wider uppercase">
                        {{ __('RENVOYER_Lien_Vérification') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-mono text-[9px] text-primary-500 uppercase">
                            {{ __('TRANSMISSION_EFFECTUÉE : Vérifiez votre boîte mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-6 pt-4">
            <x-primary-button>{{ __('SAUVEGARDER_MODIFICATIONS') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="font-mono text-[10px] text-primary-500 uppercase tracking-widest"
                >{{ __('SYSTÈME_À_JOUR.') }}</p>
            @endif
        </div>
    </form>
</section>
