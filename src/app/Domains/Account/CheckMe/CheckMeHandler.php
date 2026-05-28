<?php declare(strict_types=1);

namespace App\Domains\Account\CheckMe;

use App\Models\User;
use App\Shared\Handler;
use App\Shared\Result;

final class CheckMeHandler extends Handler
{
    /**
     * @phpstan-param CheckMeQuery $checkMeQuery
     * @phpstan-return Result<User, CheckMeError>
     */
    public function handle(CheckMeQuery $checkMeQuery): Result
    {
        if (!$checkMeQuery->user instanceof User) {
            return Result::failure(
                error: CheckMeError::NotAuthenticated
            );
        }

        return Result::success(value: $checkMeQuery->user);
    }
}
