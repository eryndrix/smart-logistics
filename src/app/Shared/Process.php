<?php declare(strict_types=1);

namespace App\Shared;

use Illuminate\Pipeline\Pipeline;

/**
 * @phpstan-template TPayload of mixed
 * @phpstan-template TResult of mixed
 */
abstract class Process
{
    /**
     * @phpstan-var list<class-string>
     */
    protected array $handlers = [];

    /**
     * @phpstan-param TPayload $payload
     * @phpstan-return TResult
     */
    public function run(mixed $payload)
    {
        /** @phpstan-var Pipeline */
        $pipeline = resolve(
            name: Pipeline::class
        );

        /** @phpstan-var TResult */
        return $pipeline->send(
            passable: $payload
        )->through(
            pipes: $this->handlers
        )->thenReturn();
    }
}
