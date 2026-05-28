<?php declare(strict_types=1);

namespace App\Domains\Account\Login\Handlers;

use App\Models\User;
use App\Domains\Account\Login\LoginContext;
use Illuminate\Support\Facades\Hash;
use App\Domains\Account\Login\LoginError;
use App\Shared\Result;

final class CheckPasswordHandler
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

        $password = $context->getPassword();

        $isPasswordCorrect = Hash::check(
            value: $password,
            hashedValue: $user->password
        );

        if (!$isPasswordCorrect) {
            $error = LoginError::InvalidPassword;
            return Result::failure(error: $error);
        }

        return $next($context);
    }
}
