<?php

return [

    'free' => [
        'label'          => 'Gratuit',
        'analyses_limit' => 3,       // par mois
        'pdf_export'     => false,
        'competitors'    => false,
        'whatsapp'       => false,
        'history'        => false,
        'price_usd'      => 0,
    ],

    'starter' => [
        'label'          => 'Starter',
        'analyses_limit' => 30,
        'pdf_export'     => true,
        'competitors'    => true,
        'whatsapp'       => false,
        'history'        => true,
        'price_usd'      => 10,
        'stripe_price_id' => env('STRIPE_STARTER_PRICE_ID'),
    ],

    'pro' => [
        'label'          => 'Pro',
        'analyses_limit' => -1,      // illimité
        'pdf_export'     => true,
        'competitors'    => true,
        'whatsapp'       => true,
        'history'        => true,
        'price_usd'      => 29,
        'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
    ],

    'agency' => [
        'label'          => 'Agency',
        'analyses_limit' => -1,
        'pdf_export'     => true,
        'competitors'    => true,
        'whatsapp'       => true,
        'history'        => true,
        'price_usd'      => 79,
        'stripe_price_id' => env('STRIPE_AGENCY_PRICE_ID'),
    ],

];
