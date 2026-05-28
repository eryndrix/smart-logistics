<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Buses\CommandBusInterface;
use App\Domains\Message\MessageCommand;
use App\Http\Requests\MessageRequest;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Route;
use Spatie\RouteAttributes\Attributes\WhereIn;
use App\Http\Responders\MessageResponder;
use App\Http\Responses\ApiResponse;

/**
 * @phpstan-template TCommandBus of CommandBusInterface
 */
#[Prefix(prefix: 'v1')]
#[Middleware(middleware: 'auth:api')]
final class MessageController extends Controller
{
    /**
     * @phpstan-var MessageResponder
     */
    private readonly MessageResponder $messageResponder;

    /**
     * @phpstan-param TCommandBus $commandBus
     */
    public function __construct(
        private readonly CommandBusInterface $commandBus
    ) {
        $this->messageResponder = new MessageResponder();
    }

    /**
     * @phpstan-param MessageRequest $messageRequest
     * @phpstan-return ApiResponse
     */
    #[WhereIn('priority', ['urgent', 'normal'])]
    #[Route(methods: 'POST', uri: '/messages/{priority}')]
    public function store(MessageRequest $messageRequest): ApiResponse
    {
        /**
         * @phpstan-var \App\Shared\Result<string, null> $result
         */
        $result = $this->commandBus->send(
            command: MessageCommand::fromRequest(request: $messageRequest)
        );

        return $this->messageResponder->store(result: $result);
    }
}
