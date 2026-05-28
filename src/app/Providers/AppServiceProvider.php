<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\Token;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\TelescopeServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * @phpstan-var int
     */
    private const int SLOW_QUERY_THRESHOLD_MS = 100;

    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->registerTelescopeIfAvailable();
    }

    /**
     * @phpstan-return void
     */
    public function boot(): void
    {
        $this->configureEloquent();
        $this->configureUrl();
        $this->configureSqlLogging();
        $this->configureRateLimiter();
        $this->configureSanctum();
    }

    /**
     * @phpstan-return void
     */
    private function registerTelescopeIfAvailable(): void
    {
        if (!$this->isLocalEnvironment() || !$this->hasTelescope()) {
            return;
        }

        $this->app->register(
            provider: TelescopeServiceProvider::class
        );
    }

    /**
     * @phpstan-return bool
     */
    private function isLocalEnvironment(): bool
    {
        return $this->app->environment('local') === 'local';
    }

    /**
     * @phpstan-return bool
     */
    private function hasTelescope(): bool
    {
        return class_exists(
            class: TelescopeServiceProvider::class
        );
    }

    /**
     * @phpstan-return void
     */
    private function configureEloquent(): void
    {
        Model::shouldBeStrict(shouldBeStrict: true);
        Model::preventLazyLoading(
            value: $this->shouldPreventLazyLoading()
        );
    }

    /**
     * @phpstan-return bool
     */
    private function shouldPreventLazyLoading(): bool
    {
        return $this->app->environment('production') !== 'production';
    }

    /**
     * @phpstan-return void
     */
    private function configureUrl(): void
    {
        if ($this->isProductionEnvironment()) {
            URL::forceScheme(scheme: 'https');
        }
    }

    /**
     * @phpstan-return bool
     */
    private function isProductionEnvironment(): bool
    {
        return $this->app->environment('production') === 'production';
    }

    /**
     * @phpstan-return void
     */
    private function configureSqlLogging(): void
    {
        if (!$this->shouldLogSlowQueries()) {
            return;
        }

        DB::listen(callback: function (QueryExecuted $queryExecuted): void {
            $this->logIfSlowQuery(queryExecuted: $queryExecuted);
        });
    }

    /**
     * @phpstan-return bool
     */
    private function shouldLogSlowQueries(): bool
    {
        return in_array(
            needle: $this->app->environment(),
            haystack: ['local', 'staging'],
            strict: true
        );
    }

    /**
     * @phpstan-param QueryExecuted $queryExecuted
     *
     * @phpstan-return void
     */
    private function logIfSlowQuery(QueryExecuted $queryExecuted): void
    {
        $executionTime = $queryExecuted->time;

        if ($executionTime < self::SLOW_QUERY_THRESHOLD_MS) {
            return;
        }

        Log::warning(
            message: 'Slow SQL query',
            context: [
                'time' => round(
                    num: $executionTime,
                    precision: 2
                ) . 'ms',
                'sql' => $queryExecuted->sql,
                'bindings' => $this->formatBindings(
                    bindings: $queryExecuted->bindings
                ),
                'connection' => $queryExecuted->connectionName,
            ]
        );
    }

    /**
     * @phpstan-param array<array-key, mixed> $bindings
     * @phpstan-return string
     */
    private function formatBindings(array $bindings): string
    {
        return json_encode(
            value: $bindings,
            flags: JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    /**
     * @phpstan-return void
     */
    private function configureRateLimiter(): void
    {
        RateLimiter::for(
            name: 'api',
            callback: function (Request $request): mixed {
                return Limit::perMinute(maxAttempts: 60)->by(
                    key: $this->getUserIdOrIp(request: $request)
                );
            }
        );
    }

    /**
     * @phpstan-param Request $request
     * @phpstan-return int|string|null
     */
    private function getUserIdOrIp(Request $request): int|string|null
    {
        $user = $request->user();

        if (!$user instanceof Authenticatable) {
            return $request->ip();
        }

        /** @phpstan-var int|string|null $userId */
        $userId = $user->getAuthIdentifier();

        return $userId ?? $request->ip();
    }

    /**
     * @phpstan-return void
     */
    private function configureSanctum(): void
    {
        Sanctum::usePersonalAccessTokenModel(model: Token::class);
    }
}
