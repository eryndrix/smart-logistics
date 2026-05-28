<?php declare(strict_types=1);

namespace App\External;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

abstract class ExternalApiBootstrap extends ServiceProvider
{
    /**
     * @phpstan-var string
     */
    protected string $service;

    /**
     * @phpstan-return void
     */
    abstract protected function initialize(): void;

    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->app->singleton(
            abstract: $this->service,
            concrete: function (): ExternalApiDispatcher {
                $baseUrl = config(
                    key: sprintf('services.%s.base_url', $this->service)
                );

                if (!is_string(value: $baseUrl)
                    || $baseUrl === ''
                ) {
                    throw new \RuntimeException(
                        message: 'Missing base URL for service ' . $this->service
                    );
                }

                $pendingRequest = Http::baseUrl(url: $baseUrl)
                    ->timeout(seconds: 15)
                    ->connectTimeout(seconds: 5)
                    ->asJson()
                    ->acceptJson();

                return new ExternalApiDispatcher(
                    pendingRequest: $pendingRequest
                );
            }
        );

        $this->initialize();
    }
}
