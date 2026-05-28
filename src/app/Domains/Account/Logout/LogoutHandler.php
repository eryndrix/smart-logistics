<?php declare(strict_types=1);

namespace App\Domains\Account\Logout;

use App\Shared\Handler;
use Laravel\Sanctum\PersonalAccessToken;
use App\Shared\Result;

final class LogoutHandler extends Handler
{
    /**
     * @phpstan-param LogoutCommand $command
     * @phpstan-return Result<bool, LogoutError>
     */
    public function handle(LogoutCommand $command): Result
    {
        $token = $command->user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $deleted = $token->delete();

            if ($deleted !== true) {
                return Result::failure(
                    error: LogoutError::TokenDeletionFailed
                );
            }
        }

        return Result::success(value: true);
    }
}
