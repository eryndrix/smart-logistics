<?php declare(strict_types=1);

namespace App\Domains\Account\CheckMe;

use Illuminate\Http\Response as Status;

enum CheckMeError: string
{
    /**
     * Indicates the user is not authenticated.
     */
    case NotAuthenticated = 'not_authenticated';

    /**
     * @phpstan-return int
     */
    public function status(): int
    {
        return Status::HTTP_UNAUTHORIZED;
    }

    /**
     * @phpstan-return string
     */
    public function message(): string
    {
        return 'auth.not_authenticated';
    }
}
