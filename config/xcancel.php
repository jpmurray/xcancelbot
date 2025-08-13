<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Target Conversion Domain
    |--------------------------------------------------------------------------
    |
    | The domain to use when converting Twitter/X links.
    |
    */
    'domain' => env('XCANCEL_DOMAIN', 'xcancel.com'),

    /*
    |--------------------------------------------------------------------------
    | Link Detection Patterns
    |--------------------------------------------------------------------------
    |
    | The regex patterns used to detect and convert links. The key is used
    | for statistics tracking. The replacement should use capture groups.
    |
    */
    'patterns' => [
        'twitter' => [
            'match' => '/https?:\/\/(?:www\.)?twitter\.com\/(\S+)/',
            'replace' => 'https://{domain}/$1',
        ],
        'x' => [
            'match' => '/https?:\/\/(?:www\.)?x\.com\/(\S+)/',
            'replace' => 'https://{domain}/$1',
        ],
    ],
];
