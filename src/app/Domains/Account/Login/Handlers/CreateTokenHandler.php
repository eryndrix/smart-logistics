<?php declare(strict_types=1);

namespace App\Domains\Account\Login\Handlers;

use App\Shared\Handler;
use App\Domains\Account\Login\LoginContext;
use App\Models\User;
use App\Domains\Account\Login\LoginError;
use App\Shared\Result;

final class CreateTokenHandler extends Handler
{
    /**
     * @phpstan-param LoginContext $context
     * @phpstan-param \Closure(LoginContext):Result<
     *     string,
     *     LoginError
     * > $next
     *
     * @phpstan-return Result<string, LoginError>
     */
    public function handle(
        LoginContext $context, \Closure $next): Result
    {
        $user = $context->getUser();

        if (!$user instanceof User) {
            $error = LoginError::UserNotFound;
            return Result::failure(error: $error);
        }

        $rememberMe = $context->getRememberMe();
        $tokenName = $rememberMe
            ? 'auth_token_remember'
            : 'auth_token';

        try {
            $token = $user->createToken(
                name: $tokenName,
                abilities: ['*'],
                expiresAt: $rememberMe
                    ? now()->addDays(value: 30)
                    : null
            )->plainTextToken;

            return Result::success(value: $token);
        }

        catch (\Exception $e) {
            $error = LoginError::TokenCreationFailed;
            return Result::failure(error: $error);
        }
    }
}
