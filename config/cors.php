<?php

$fromEnv = array_filter(
    array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', '')))
);

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    /*
     * JWT is sent via Authorization; cookies are not used for API auth, so credentialed CORS is off.
     * Default * fixes browsers when deploy URL / config cache does not list every frontend origin.
     * Override on Render: CORS_ALLOWED_ORIGINS=https://your-app.vercel.app,http://localhost:5173
     */
    'allowed_origins' => ! empty($fromEnv) ? array_values($fromEnv) : ['*'],

    'allowed_origins_patterns' => [
        '#^https://[\w.-]+\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];

