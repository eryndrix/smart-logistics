<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Buses\QueryBusInterface;
use App\Buses\CommandBusInterface;
use App\Domains\Account\CheckMe\CheckMeQuery;
use App\Domains\Account\Login\LoginCommand;
use App\Domains\Account\Logout\LogoutCommand;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Route;
use Illuminate\Http\Request;
use App\Http\Responders\AuthResponder;
use App\Http\Responses\ApiResponse;

/**
 * @phpstan-template TCommandBus of CommandBusInterface
 * @phpstan-template TQueryBus of QueryBusInterface
 */
#[Prefix(prefix: 'v1')]
final class AuthController extends Controller
{
    /**
     * @phpstan-var AuthResponder
     */
    private readonly AuthResponder $authResponder;

    /**
     * @phpstan-param TCommandBus $commandBus
     * @phpstan-param TQueryBus $queryBus
     */
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus
    ) {
        $this->authResponder = new AuthResponder();
    }

    /**
     * @phpstan-param Request $request
     * @phpstan-return ApiResponse
     */
    #[Route(methods: 'POST', uri: '/login')]
    public function login(Request $request): ApiResponse
    {
        /**
         * @phpstan-var \App\Shared\Result<
         *     string,
         *     \App\Domains\Account\Login\LoginError
         * > $result
         */
        $result = $this->commandBus->send(
            command: LoginCommand::fromRequest(request: $request)
        );

        return $this->authResponder->login(result: $result);
    }

    /**
     * @phpstan-param Request $request
     * @phpstan-return ApiResponse
     */
    #[Route(methods: 'POST', uri: '/logout', middleware: 'auth:sanctum')]
    public function logout(Request $request): ApiResponse
    {
        /** @phpstan-var \App\Models\User $user */
        $user = $request->user();

        /**
         * @phpstan-var \App\Shared\Result<
         *     bool,
         *     \App\Domains\Account\Logout\LogoutError
         * > $result
         */
        $result = $this->commandBus->send(
            command: new LogoutCommand(user: $user)
        );

        return $this->authResponder->logout(result: $result);
    }

    /**
     * @phpstan-param Request $request
     *
     * @phpstan-return ApiResponse
     */
    #[Route(methods: 'GET', uri: '/check-me', middleware: 'auth:sanctum')]
    public function checkMe(Request $request): ApiResponse
    {
        /** @phpstan-var \App\Models\User $user */
        $user = $request->user();

        /**
         * @phpstan-var \App\Shared\Result<
         *     \App\Models\User,
         *     \App\Domains\Account\CheckMe\CheckMeError
         * > $result
         */
        $result = $this->queryBus->ask(
            query: new CheckMeQuery(user: $user)
        );

        return $this->authResponder->checkMe(result: $result);
    }
}
