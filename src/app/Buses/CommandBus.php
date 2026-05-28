<?php declare(strict_types=1);

namespace App\Buses;

use App\Shared\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Shared\Result;

/**
 * @implements CommandBusInterface<
 *     Command,
 *     Result<mixed, mixed>
 * >
 */
final class CommandBus implements CommandBusInterface
{
    /**
     * @phpstan-param Dispatcher $dispatcher
     */
    public function __construct(
        private Dispatcher $dispatcher
    ) {}

    /**
     * @phpstan-param Command $command
     * @phpstan-return Result<mixed, mixed>
     */
    public function send(Command $command): Result
    {
        /** @phpstan-var Result<mixed, mixed> $result */
        $result = $this->dispatcher->dispatch(
            command: $command
        );

        return $result;
    }

    /**
     * @phpstan-param array<
     *     class-string<Command>,
     *     class-string
     * > $map
     */
    public function register(array $map): void
    {
        $this->dispatcher->map(map: $map);
    }
}
