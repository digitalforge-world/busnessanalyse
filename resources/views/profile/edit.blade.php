@extends('layouts.app')

@section('title', 'USER_PROFILE_SETTINGS')

@section('content')
<div class="max-w-4xl mx-auto space-y-12 py-8">
    {{-- Header --}}
    <div class="space-y-4 anim-fade-up">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-primary-500 shadow-[0_0_8px_#00FF88]"></div>
            <div class="font-mono text-[10px] text-primary-500 uppercase tracking-[0.3em]">KERNEL_DASHBOARD // USER_ACCOUNT</div>
        </div>
        <h1 class="text-5xl font-display text-white tracking-tight">PARAMÈTRES_SYSTÈME</h1>
        <p class="text-muted font-light italic text-base">Gérez votre identité numérique et vos protocoles de sécurité.</p>
    </div>

    <div class="space-y-8">
        {{-- Section: Profil --}}
        <div class="card p-10 anim-fade-up" style="animation-delay: 0.1s">
            <div class="flex items-center gap-4 mb-8">
                <div class="font-mono text-[9px] text-muted-foreground border border-muted2/30 px-2 py-1 rounded uppercase tracking-widest">01_IDENTITY</div>
                <div class="h-px flex-1 bg-muted2/10"></div>
            </div>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Section: Mot de passe --}}
        <div class="card p-10 anim-fade-up" style="animation-delay: 0.2s">
            <div class="flex items-center gap-4 mb-8">
                <div class="font-mono text-[9px] text-muted-foreground border border-muted2/30 px-2 py-1 rounded uppercase tracking-widest">02_AUTHENTICATION</div>
                <div class="h-px flex-1 bg-muted2/10"></div>
            </div>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Section: Suppression --}}
        <div class="card p-10 border-red-500/10 hover:border-red-500/20 transition anim-fade-up" style="animation-delay: 0.3s">
            <div class="flex items-center gap-4 mb-8">
                <div class="font-mono text-[9px] text-red-400 border border-red-500/20 px-2 py-1 rounded uppercase tracking-widest">03_TERMINATION</div>
                <div class="h-px flex-1 bg-red-500/5"></div>
            </div>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
