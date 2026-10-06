<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // V2 — OpenAI AI Content Assistant (modular; add more AI features on top of this)
    // WhatsApp Cloud tools (inbox, AI knowledge base, integration). The super admin always has them
    // for the Order Click number; merchants only when WHATSAPP_TOOLS=true in .env.
    'whatsapp_cloud' => [
        'tools' => (bool) env('WHATSAPP_TOOLS', false),
    ],

    // AI assistant on every store page (answers from that store's data only). On by default when
    // an OpenAI key is set; STORE_ASSISTANT=false in .env switches it off everywhere.
    'store_assistant' => [
        'enabled' => (bool) env('STORE_ASSISTANT', true),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-5-mini'),
        // The AI store designer can use a stronger model than everyday text help.
        'design_model' => env('OPENAI_DESIGN_MODEL', env('OPENAI_MODEL', 'gpt-5-mini')),
        'endpoint' => env('OPENAI_ENDPOINT', 'https://api.openai.com/v1/responses'),
    ],

];
