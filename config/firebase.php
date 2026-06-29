<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase services integration
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),

    'project_id' => env('FIREBASE_PROJECT_ID', 'website-kkn078'),

    'database_url' => env('FIREBASE_DATABASE_URL', null),

    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'website-kkn078.firebaseapp.com'),

    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'website-kkn078.firebasestorage.app'),

    // Public reads are cached locally to avoid a network round-trip on every page view.
    'cache_ttl' => (int) env('FIREBASE_CACHE_TTL', 300),

    // Fail fast when Google APIs cannot be reached, leaving enough time for
    // the application to serve cached data before PHP's execution limit.
    'connect_timeout' => (float) env('FIREBASE_CONNECT_TIMEOUT', 8),
    'request_timeout' => (float) env('FIREBASE_REQUEST_TIMEOUT', 5),
    'write_timeout' => (float) env('FIREBASE_WRITE_TIMEOUT', 15),
    'circuit_ttl' => (int) env('FIREBASE_CIRCUIT_TTL', 30),

    /*
    |--------------------------------------------------------------------------
    | Firestore Configuration
    |--------------------------------------------------------------------------
    */

    'firestore' => [
        'enabled' => env('FIRESTORE_ENABLED', true),
        'database' => env('FIRESTORE_DATABASE', '(default)'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */

    'storage' => [
        'enabled' => env('STORAGE_ENABLED', true),
        'bucket' => env('FIREBASE_STORAGE_BUCKET', 'website-kkn078.firebasestorage.app'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Configuration
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'enabled' => env('AUTH_ENABLED', true),
    ],
];
