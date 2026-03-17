<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Page d'accueil publique
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Changer la langue
Route::post('/langue/{locale}', function (string $locale) {
    $locales = ['fr', 'en', 'ar', 'pt'];
    if (in_array($locale, $locales)) {
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        session(['locale' => $locale]);
    }
    return back();
})->name('langue.changer');

// Application principale (authentification requise)
Route::middleware(['auth', 'verified', 'App\Http\Middleware\SetLocale'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analyser', [AnalysisController::class, 'index'])->name('analysis.index');

    // Analyse
    Route::post('/analyser', [AnalysisController::class, 'analyser'])
        ->middleware('App\Http\Middleware\CheckAnalysisQuota')
        ->name('analysis.analyser');

    Route::get('/entreprise/{slug}', [AnalysisController::class, 'show'])->name('analysis.show');

    Route::post('/entreprise/{slug}/concurrents', [AnalysisController::class, 'analyserConcurrents'])
        ->name('analysis.concurrents');

    Route::post('/entreprise/{slug}/whatsapp', [AnalysisController::class, 'envoyerWhatsApp'])
        ->name('analysis.whatsapp');

    // PDF & EXPORT
    Route::get('/entreprise/{slug}/pdf', [PdfController::class, 'telecharger'])
        ->name('analysis.pdf');
    Route::get('/entreprise/{slug}/excel', [AnalysisController::class, 'exporterExcel'])
        ->name('analysis.excel');

    // Abonnements
    Route::get('/abonnement', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/abonnement/stripe', [SubscriptionController::class, 'stripe'])->name('subscription.stripe');
    Route::post('/abonnement/cinetpay', [SubscriptionController::class, 'cinetpay'])->name('subscription.cinetpay');
    Route::get('/abonnement/upgrade', function() {
        return view('subscription.upgrade');
    })->name('subscription.upgrade');

});

// Webhook Stripe (Public car appelé par Stripe)
Route::post('/webhook/stripe', [SubscriptionController::class, 'stripeWebhook']);

// Admin
Route::middleware(['auth', 'App\Http\Middleware\AdminOnly'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])->name('admin.users');
    Route::get('/analyses', [AdminController::class, 'analyses'])->name('admin.analyses');
});

// Auth Breeze
require __DIR__.'/auth.php';
