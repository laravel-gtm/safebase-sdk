<?php

declare(strict_types=1);

return [
    'base_url' => env('SAFEBASE_BASE_URL', 'https://app.safebase.io/api/ext/v1/rest'),
    'token' => env('SAFEBASE_TOKEN'),
    'auth_header' => env('SAFEBASE_AUTH_HEADER', 'X-Api-Key'),
];
