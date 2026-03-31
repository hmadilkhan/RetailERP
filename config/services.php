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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'shopify' => [
        'token' => env('SHOPIFY_TOKEN'),
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_id' => env('WHATSAPP_PHONE_NUMBER_ID', env('WHATSAPP_PHONE_ID')),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'template_lang' => env('WHATSAPP_TEMPLATE_LANG', 'en'),
        'templates' => [
            'fbr_report' => env('WHATSAPP_TEMPLATE_FBR_REPORT', 'fbr_report_delivery'),
            'sales_report' => env('WHATSAPP_TEMPLATE_SALES_REPORT', 'sales_report_delivery'),
            'billing_invoice' => env('WHATSAPP_TEMPLATE_BILLING_INVOICE', 'report'),
        ],
    ],

];
