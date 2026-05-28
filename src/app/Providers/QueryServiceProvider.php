<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Journal\JournalHandler;
use App\Domains\Journal\JournalQuery;
use App\Domains\Account\CheckMe\CheckMeHandler;
use App\Domains\Account\CheckMe\CheckMeQuery;
use App\Buses\QueryBusInterface;

final class QueryServiceProvider extends ServiceProvider
{
    /**
     * @phpstan-param QueryBusInterface<
     *     \App\Shared\Query,
     *     \App\Shared\Handler,
     *     \App\Shared\Result<mixed, mixed>
     * > $queryBus
     *
     * @phpstan-return void
     */
    public function boot(QueryBusInterface $queryBus): void
    {
        $queryBus->register(map: [
            CheckMeQuery::class => CheckMeHandler::class,
            JournalQuery::class => JournalHandler::class,
        ]);
    }
}
