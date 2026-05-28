<?php declare(strict_types=1);

namespace App\Domains\Account\Login\Handlers;

use App\Shared\Handler;
use App\Domains\Account\Login\LoginContext;
use Illuminate\Support\Str;
use App\Domains\Account\Login\LoginError;
use App\Shared\Result;

final class UpdateRememberTokenHandler extends Handler
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
        $rememberMe = $context->getRememberMe();

        $rememberToken = $rememberMe
            ? Str::random(length: 60)
            : null;

        $user?->forceFill(attributes: [
            'remember_token' => $rememberToken,
        ])->saveQuietly();

        return $next($context);
    }
}
