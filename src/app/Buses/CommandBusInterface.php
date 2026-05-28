<?php declare(strict_types=1);

namespace App\Buses;

use App\Shared\Command;
use App\Shared\Result;

/**
 * @phpstan-template TCommand of Command
 * @phpstan-template TResult of Result<mixed, mixed>
 */
interface CommandBusInterface
{
    /**
     * @phpstan-param TCommand $command
     * @phpstan-return TResult
     */
    public function send(Command $command): Result;

    /**
     * @phpstan-param array<class-string<TCommand>, class-string> $map
     */
    public function register(array $map): void;
}
