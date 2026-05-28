<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
    App\Providers\BusServiceProvider::class,
    App\Providers\CommandServiceProvider::class,
    App\Providers\QueryServiceProvider::class,
    App\Domains\Message\Transports\TransportProviderRegistrar::class,
    App\Domains\Message\Transports\TransportChannelRegistrar::class,
    App\External\Integrations\SubscriberProvider::class,
];
