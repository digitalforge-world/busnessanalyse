@extends('layouts.app')
@section('title', 'Améliorez votre offre')

@section('content')
<div class="max-w-2xl mx-auto text-center py-20 animate-fade-in-up">
    <div class="bg-primary-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8">
        <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
    </div>
    
    <h1 class="text-4xl font-black text-gray-900 mb-4">Accédez à plus de fonctionnalités</h1>
    <p class="text-lg text-gray-500 mb-10 leading-relaxed max-w-lg mx-auto">
        {{ session('message') ?? "Vous avez atteint une limite de votre plan actuel ou cette fonctionnalité nécessite un abonnement supérieur." }}
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('subscription.index') }}" class="bg-primary-600 text-white px-10 py-5 rounded-3xl font-black text-lg hover:bg-primary-700 shadow-xl shadow-primary-200 transition transform hover:-translate-y-1">
            Voir les tarifs
        </a>
        <a href="{{ route('dashboard') }}" class="text-gray-500 font-bold hover:text-gray-700 transition">
            Retour au dashboard
        </a>
    </div>

    <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-6 text-left">
        @foreach([
            ['titre' => 'Analyses Illimitées', 'desc' => 'Libérez votre potentiel'],
            ['titre' => 'Rapports PDF', 'desc' => 'Prêt pour impression'],
            ['titre' => 'Historique', 'desc' => 'Suivi de croissance']
        ] as $feature)
        <div class="p-6 bg-white rounded-2xl border border-gray-100">
            <div class="text-primary-500 font-black mb-1">{{ $feature['titre'] }}</div>
            <div class="text-xs text-gray-400">{{ $feature['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection
