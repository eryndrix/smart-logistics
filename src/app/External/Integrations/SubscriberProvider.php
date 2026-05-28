<?php declare(strict_types=1);

namespace App\External\Integrations;

use App\External\ExternalApiBootstrap;
use App\Shared\Contracts\SubscriberContract;
use App\External\ExternalApiInterface;

final class SubscriberProvider extends ExternalApiBootstrap
{
    /**
     * @phpstan-var string
     */
    protected string $service = 'subscriber';

    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * @phpstan-return void
     */
    protected function initialize(): void
    {
        $this->app->when(
            concrete: SubscriberApiGate::class
        )->needs(
            abstract: ExternalApiInterface::class
        )->give(
            implementation: fn() => resolve(
                name: $this->service
            )
        );

        $this->app->bind(
            abstract: SubscriberContract::class,
            concrete: SubscriberService::class
        );
    }
}
