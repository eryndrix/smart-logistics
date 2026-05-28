<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Account\Login\LoginCommand;
use App\Domains\Account\Login\LoginProcess;
use App\Domains\Account\Logout\LogoutCommand;
use App\Domains\Account\Logout\LogoutHandler;
use App\Domains\Message\MessageCommand;
use App\Domains\Message\MessageProcess;
use App\Buses\CommandBusInterface;

final class CommandServiceProvider extends ServiceProvider
{
    /**
     * @phpstan-param CommandBusInterface<
     *     \App\Shared\Command,
     *     \App\Shared\Result<mixed, mixed>
     * > $commandBus
     *
     * @phpstan-return void
     */
    public function boot(CommandBusInterface $commandBus): void
    {
        /**
         * @phpstan-var array<
         *     class-string<\App\Shared\Command>,
         *     class-string
         * > $map
         */
        $map = [
            LoginCommand::class => LoginProcess::class,
            LogoutCommand::class => LogoutHandler::class,
            MessageCommand::class => MessageProcess::class,
        ];

        $commandBus->register(map: $map);
    }
}
