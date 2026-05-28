<?php declare(strict_types=1);

namespace App\Buses;

use App\Shared\Query;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Shared\Result;

/**
 * @phpstan-implements QueryBusInterface<
 *     Query,
 *     \App\Shared\Handler,
 *     Result<mixed, mixed>
 * >
 */
final class QueryBus implements QueryBusInterface
{
    /**
     * @phpstan-param Dispatcher $dispatcher
     */
    public function __construct(
        private Dispatcher $dispatcher
    ) {}

    /**
     * @phpstan-param Query $query
     * @phpstan-return Result<mixed, mixed>
     */
    public function ask(Query $query): Result
    {
        /** @phpstan-var Result<mixed, mixed> $result */
        $result = $this->dispatcher->dispatch(
            command: $query
        );

        return $result;
    }

    /**
     * @phpstan-param array<
     *     class-string<Query>,
     *     class-string<\App\Shared\Handler>
     * > $map
     * 
     * @phpstan-return void
     */
    public function register(array $map): void
    {
        $this->dispatcher->map(map: $map);
    }
}
