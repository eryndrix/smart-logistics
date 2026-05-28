<?php declare(strict_types=1);

namespace App\External\Integrations;

use App\Shared\Contracts\SubscriberContract;

final class SubscriberService implements SubscriberContract
{
    /**
     * @phpstan-param SubscriberApiGate $apiGate
     * @phpstan-param SubscriberCache $cache
     */
    public function __construct(
        private readonly SubscriberApiGate $apiGate,
        private readonly SubscriberCache $cache
    ) {}

    /**
     * @phpstan-return void
     */
    private function initialize(): void
    {
        $items = $this->apiGate->load();
        $this->cache->store(items: $items);
    }

    /**
     * @phpstan-param string $id
     *
     * @phpstan-return array<string, mixed>
     */
    public function findById(string $id): array
    {
        $result = $this->cache->findById(
            id: $id
        );

        if (blank(value: $result)) {
            $this->initialize();

            $result = $this->cache->findById(
                id: $id
            );
        }

        return $result;
    }
}
