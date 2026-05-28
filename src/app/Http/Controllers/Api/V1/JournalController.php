<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Buses\QueryBusInterface;
use App\Domains\Journal\JournalQuery;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Route;
use Spatie\RouteAttributes\Attributes\WhereUuid;
use App\Http\Responders\JournalResponder;
use App\Http\Responses\ApiResponse;

/**
 * @phpstan-template TQueryBus of QueryBusInterface
 */
#[Prefix(prefix: 'v1')]
#[Middleware(middleware: 'auth:api')]
final class JournalController extends Controller
{
    /**
     * @phpstan-var JournalResponder
     */
    private readonly JournalResponder $journalResponder;

    /**
     * @phpstan-param TQueryBus $queryBus
     */
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
        $this->journalResponder = new JournalResponder();
    }

    /**
     * @phpstan-param string $subscriberId
     * @phpstan-return ApiResponse
     */
    #[WhereUuid(param: 'subscriberId')]
    #[Route(methods: 'GET', uri: '/journals/{subscriberId}')]
    public function index(string $subscriberId): ApiResponse
    {
        $result = $this->queryBus->ask(
            query: new JournalQuery(subscriberId: $subscriberId)
        );

        return $this->journalResponder->index(result: $result);
    }
}
