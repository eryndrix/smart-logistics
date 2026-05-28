<?php declare(strict_types=1);

namespace App\Buses;

use App\Shared\Query;
use App\Shared\Result;

/**
 * @phpstan-template TQuery of Query
 * @phpstan-template THandler of \App\Shared\Handler
 * @phpstan-template TResult of Result
 */
interface QueryBusInterface
{
    /**
     * @phpstan-param TQuery $query
     * @phpstan-return TResult
     */
    public function ask(Query $query): Result;

    /**
     * @phpstan-param array<
     *     class-string<TQuery>,
     *     class-string<THandler>
     * > $map
     *
     * @phpstan-return void
     */
    public function register(array $map): void;
}
