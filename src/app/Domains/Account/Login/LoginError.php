<?php declare(strict_types=1);

namespace App\Domains\Account\Login;

use Illuminate\Http\Response as Status;

enum LoginError: string
{
    /**
     * User record not found.
     */
    case UserNotFound = 'user_not_found';

    /**
     * Password does not match.
     */
    case InvalidPassword = 'invalid_password';

    /**
     * Failed to create access token.
     */
    case TokenCreationFailed = 'token_creation_failed';

    /**
     * Login attempts exceeded allowed limit.
     */
    case TooManyLoginAttempts = 'too_many_login_attempts';

    /**
     * @phpstan-return int
     */
    public function status(): int
    {
        return match ($this) {
            self::UserNotFound, self::InvalidPassword => Status::HTTP_UNAUTHORIZED,
            self::TooManyLoginAttempts => Status::HTTP_TOO_MANY_REQUESTS,
            self::TokenCreationFailed => Status::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    /**
     * @phpstan-return string
     */
    public function message(): string
    {
        return match ($this) {
            self::UserNotFound, self::InvalidPassword => 'auth.invalid_credentials',
            self::TooManyLoginAttempts => 'auth.throttle',
            self::TokenCreationFailed => 'auth.token_creation_failed',
        };
    }
}
