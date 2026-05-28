<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Value Object Identifier Mappings
    |--------------------------------------------------------------------------
    |
    | Here you may define which value object class should be used for each
    | Eloquent model. These mappings allow your custom casts to resolve the
    | correct identifier implementation without hardcoding model-specific
    | references inside the cast classes themselves.
    |
    */

    'id' => [
        App\Models\Role::class => App\Shared\ValueObjects\Id\RoleId::class,
        App\Models\User::class => App\Shared\ValueObjects\Id\UserId::class,
        App\Models\Message::class => App\Shared\ValueObjects\Id\MessageId::class,
        App\Models\Recipient::class => App\Shared\ValueObjects\Id\RecipientId::class,
        App\Models\Journal::class => App\Shared\ValueObjects\Id\JournalId::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Value Object Slug Mappings
    |--------------------------------------------------------------------------
    |
    | Here you may define which value object class should be used for each
    | model slug. This keeps slug casting configuration centralized and makes
    | it easy to add new model-to-slug mappings as the application grows.
    |
    */

    'slug' => [
        App\Models\Role::class => App\Shared\ValueObjects\Slug\RoleSlug::class,
    ],
];
