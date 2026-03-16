<section class="space-y-6">
    <header class="space-y-2">
        <h2 class="text-xl font-display text-red-500 tracking-wide uppercase">
            {{ __('ZONE_DANGER / SUPPRESSION_COMPTE') }}
        </h2>

        <p class="text-sm text-muted font-light leading-relaxed">
            {{ __('Une fois la session terminée, toutes vos données (analyses, logs, préférences) seront purgées. Cette action est irréversible.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('TERMINER_SESSION_DÉFINITIVEMENT') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-10 bg-ink border border-red-500/20 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-display text-white mb-4 uppercase tracking-tight">
                {{ __('CONFIRMATION_PURGE_SYSTÈME') }}
            </h2>

            <p class="text-sm text-muted font-light mb-8 leading-relaxed">
                {{ __('Veuillez entrer votre protocole de sécurité (mot de passe) pour confirmer la suppression définitive de votre instance utilisateur.') }}
            </p>

            <div class="space-y-4">
                <x-input-label for="password" value="{{ __('MOT_DE_PASSE') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('VOTRE_MOT_DE_PASSE_ICI') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('ANNULER') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('SUPPRIMER_DÉFINITIVEMENT') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
