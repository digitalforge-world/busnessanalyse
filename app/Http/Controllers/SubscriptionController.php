<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = config('plans');
        $user  = auth()->user();
        return view('subscription.index', compact('plans', 'user'));
    }

    // Redirection vers Stripe Checkout (cartes internationales)
    public function stripe(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:starter,pro,agency']]);

        $plan = config("plans.{$request->plan}");

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItem = [
            'quantity' => 1,
        ];

        if (!empty($plan['stripe_price_id'])) {
            $lineItem['price'] = $plan['stripe_price_id'];
        } else {
            $lineItem['price_data'] = [
                'currency'     => 'usd',
                'product_data' => [
                    'name' => "Abonnement {$plan['label']} — Business Intelligence",
                ],
                'unit_amount'  => $plan['price_usd'] * 100,
                'recurring'    => ['interval' => 'month'],
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [$lineItem],
            'mode'                 => 'subscription',
            'success_url'          => route('analysis.index') . '?success=1',
            'cancel_url'           => route('subscription.index') . '?cancelled=1',
            'metadata'             => ['user_id' => auth()->id(), 'plan' => $request->plan],
        ]);

        return redirect($session->url);
    }

    // CinetPay — Mobile Money (Togo, Côte d'Ivoire, Sénégal, etc.)
    public function cinetpay(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:starter,pro,agency']]);

        $plan   = config("plans.{$request->plan}");
        $user   = auth()->user();
        $transId = 'bia-' . $user->id . '-' . time();

        $montant = $plan['price_usd'] * 600; // Conversion USD → XOF approximative

        $payload = [
            'apikey'         => config('services.cinetpay.api_key'),
            'site_id'        => config('services.cinetpay.site_id'),
            'transaction_id' => $transId,
            'amount'         => $montant,
            'currency'       => 'XOF',
            'description'    => "Abonnement {$plan['label']} — Business Intelligence",
            'return_url'     => route('subscription.index') . '?success=1',
            'notify_url'     => url('/webhook/cinetpay'),
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
        ];

        $response = \Illuminate\Support\Facades\Http::post('https://api-checkout.cinetpay.com/v2/payment', $payload);
        $data     = $response->json();

        if (isset($data['data']['payment_url'])) {
            return redirect($data['data']['payment_url']);
        }

        return back()->with('error', 'Erreur CinetPay. Réessayez.');
    }

    // Active l'abonnement après paiement réussi (webhook Stripe)
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, config('services.stripe.webhook_secret'));
        } catch (\Exception $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $userId  = $session->metadata->user_id;
            $plan    = $session->metadata->plan;

            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->update(['plan' => $plan]);
                Subscription::create([
                    'user_id'                => $userId,
                    'plan'                   => $plan,
                    'stripe_subscription_id' => $session->subscription,
                    'statut'                 => 'active',
                    'expire_le'              => now()->addMonth(),
                ]);
            }
        }

        return response('OK', 200);
    }
}
