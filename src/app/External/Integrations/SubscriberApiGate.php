<?php declare(strict_types=1);

namespace App\External\Integrations;

use App\External\ExternalApiInterface;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use GuzzleHttp\Psr7\StreamWrapper;

final class SubscriberApiGate
{
    /**
     * @phpstan-param ExternalApiInterface $externalApi
     */
    public function __construct(
        private ExternalApiInterface $externalApi
    ) {}

    /**
     * @phpstan-return list<array<string, mixed>>
     */
    public function load(): array
    {
        /** @phpstan-var \Psr\Http\Message\ResponseInterface $psrResponse */
        $psrResponse = $this->externalApi->dispatch(
            method: 'GET',
            url: '/api/v1/subscribers',
        );

        $resource = StreamWrapper::getResource(
            stream: $psrResponse->getBody()
        );

        /** @phpstan-var list<array<string, mixed>> $result */
        $result = [];

        foreach (Items::fromStream(
            stream: $resource,
            options: [
                'pointer' => '/collection',
                'decoder' => new ExtJsonDecoder(
                    assoc: true
                ),
            ]
        ) as $item) {
            /** @phpstan-var array<string, mixed> $item */
            if (!isset($item['id'])) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }
}
