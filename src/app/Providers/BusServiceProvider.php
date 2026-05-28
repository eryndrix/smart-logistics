<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Buses\CommandBus;
use App\Buses\CommandBusInterface;
use App\Buses\QueryBus;
use App\Buses\QueryBusInterface;

final class BusServiceProvider extends ServiceProvider
{
    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->app->singleton(
            abstract: CommandBusInterface::class,
            concrete: CommandBus::class
        );

        $this->app->singleton(
            abstract: QueryBusInterface::class,
            concrete: QueryBus::class
        );
    }
}
