<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message Dedupe
    |--------------------------------------------------------------------------
    |
    | These options control temporary deduplication in Redis. The dedupe key
    | is stored only for a short time to protect against accidental duplicate
    | submissions, while still allowing the same message to be sent again
    | later if needed.
    |
    */

    'dedupe' => [
        'enabled' => (bool) env('MESSAGE_DEDUPE_ENABLED', true),
        'store' => env('MESSAGE_DEDUPE_STORE', 'redis'),
        'ttl_seconds' => (int) env('MESSAGE_DEDUPE_TTL_SECONDS', 900),
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Limits
    |--------------------------------------------------------------------------
    |
    | These options define rate limits for message dispatching. Limits are
    | tracked in Redis and are used to prevent sending bursts that could
    | overload external gateways or violate provider limits.
    |
    */

    'limits' => [
        'store' => env('MESSAGE_LIMITS_STORE', 'redis'),
        'per_channel_per_minute' => (int) env('MESSAGE_LIMIT_PER_CHANNEL_PER_MINUTE', 60),
        'per_recipient_per_minute' => (int) env('MESSAGE_LIMIT_PER_RECIPIENT_PER_MINUTE', 10),
        'window_seconds' => (int) env('MESSAGE_LIMIT_WINDOW_SECONDS', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | These options define how many times a failed message job may be retried
    | and which backoff delays should be applied between attempts. Only transient
    | transport and gateway errors should be treated as retryable.
    |
    */

    'retry' => [
        'attempts' => (int) env('MESSAGE_MAX_ATTEMPTS', 5),
        'backoff_seconds' => array_map(
            'intval', explode(',', env('MESSAGE_BACKOFF_SECONDS', '10,30,60,180,300'))
        ),
        'retryable_http_codes' => [408, 429, 500, 502, 503, 504],
    ],
];
