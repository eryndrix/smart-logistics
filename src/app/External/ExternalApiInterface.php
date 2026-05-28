<?php declare(strict_types=1);

namespace App\External;

interface ExternalApiInterface
{
    /**
     * @phpstan-param string $method
     * @phpstan-param string $url
     * @phpstan-param array<string, mixed> $data
     * @phpstan-param array<string, string> $headers
     *
     * @phpstan-return mixed
     */
    public function dispatch(
        string $method,
        string $url,
        array $data = [],
        array $headers = []
    ): mixed;
}
