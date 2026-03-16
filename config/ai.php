<?php

return [

    'gemini' => [
        'api_key'   => env('GEMINI_API_KEY'),
        'model'     => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'base_url'  => env('GEMINI_BASE_URL'),
        'timeout'   => 60,
        'grounding' => true,   // Active Google Search en temps réel
    ],

    'mistral' => [
        'api_key'  => env('F9RPSEdK2M7ICJPVVsUpCEkeEocJ5hoM'),
        'model'    => 'mistral-small-latest',
        'base_url' => 'https://api.mistral.ai/v1',
    ],

    'groq' => [
        'api_key'    => env('GROQ_API_KEY'),
        'model'      => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'base_url'   => env('GROQ_BASE_URL'),
        'timeout'    => 45,
        'max_tokens' => 2048,
    ],


    'analysis' => [
        'cache_ttl'  => env('ANALYSIS_CACHE_TTL', 1440),
        'max_retries' => 2,
    ],

];
