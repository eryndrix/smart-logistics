<?php declare(strict_types=1);

namespace App\Domains\Account\Login;

use App\Shared\Process;
use App\Domains\Account\Login\Handlers\CheckPasswordHandler;
use App\Domains\Account\Login\Handlers\CreateTokenHandler;
use App\Domains\Account\Login\Handlers\LoadUserByEmailHandler;
use App\Domains\Account\Login\Handlers\RateLimitHandler;
use App\Domains\Account\Login\Handlers\UpdateRememberTokenHandler;
use Illuminate\Support\Facades\Log;
use App\Shared\Result;

/**
 * @extends Process<LoginContext, Result<string, LoginError>>
 */
final class LoginProcess extends Process
{
    /**
     * @phpstan-var list<class-string>
     */
    protected array $handlers = [
        RateLimitHandler::class,
        LoadUserByEmailHandler::class,
        CheckPasswordHandler::class,
        UpdateRememberTokenHandler::class,
        CreateTokenHandler::class,
    ];

    /**
     * @phpstan-param LoginCommand $command
     * @phpstan-return Result<string, LoginError>
     */
    public function __invoke(LoginCommand $command): Result
    {
        $result = $this->run(
            payload: LoginContext::of(command: $command)
        );

        $result->mapError(
            mapper: function (LoginError $error): LoginError {
                $context = [
                    'error' => $error->value,
                    'status' => $error->status(),
                ];

                match ($error) {
                    LoginError::TokenCreationFailed => Log::error(
                        message: 'Login token creation failed.',
                        context: $context
                    ),
                    LoginError::TooManyLoginAttempts => Log::warning(
                        message: 'Login rate limited.',
                        context: $context
                    ),
                    default => Log::warning(
                        message: 'Login failed.',
                        context: $context
                    )
                };

                return $error;
            }
        );

        return $result;
    }
}
