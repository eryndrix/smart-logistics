<?php declare(strict_types=1);

namespace App\Domains\Account\Logout;

use Illuminate\Http\Response as Status;

enum LogoutError: string
{
    /**
     * Failed to delete the access token.
     */
    case TokenDeletionFailed = 'token_deletion_failed';

    /**
     * @phpstan-return int
     */
    public function status(): int
    {
        return match ($this) {
            self::TokenDeletionFailed => Status::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    /**
     * @phpstan-return string
     */
    public function message(): string
    {
        return match ($this) {
            self::TokenDeletionFailed => 'auth.logout_failed',
        };
    }
}
