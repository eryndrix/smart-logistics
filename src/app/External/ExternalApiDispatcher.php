<?php declare(strict_types=1);

namespace App\External;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

final class ExternalApiDispatcher implements ExternalApiInterface
{
    /**
     * @phpstan-param PendingRequest $pendingRequest
     */
    public function __construct(
        private readonly PendingRequest $pendingRequest
    ) {}

    /**
     * @phpstan-param string $method
     * @phpstan-param string $url
     * @phpstan-param array<string, mixed> $data
     * @phpstan-param array<string, string> $headers
     *
     * @phpstan-return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function dispatch(
        string $method,
        string $url,
        array $data = [],
        array $headers = []
    ): mixed {
        try {
            $request = clone $this->pendingRequest;
            $request = $request->withHeaders(headers: [...$headers]);

            $httpMethod = strtoupper(string: $method);

            $response = match ($httpMethod) {
                'GET' => $request->get(url: $url, query: $data),
                'POST' => $request->post(url: $url, data: $data),
                'PUT' => $request->put(url: $url, data: $data),
                'DELETE' => $request->delete(url: $url, data: $data),
                default => throw new \InvalidArgumentException(
                    message: 'Unsupported HTTP method: ' . $method
                )
            };

            if ($response->failed()) {
                $message = sprintf(
                    'API error [%s %s]: %s',
                    $method,
                    $url,
                    $response->body()
                );

                throw new \RuntimeException(message: $message);
            }

            return $response;
        }

        catch (\Exception $exception) {
            Log::error(
                message: 'HTTP request dispatch failed.',
                context: ['exception' => $exception]
            );

            throw $exception;
        }
    }
}
