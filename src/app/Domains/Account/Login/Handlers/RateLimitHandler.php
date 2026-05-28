<?php declare(strict_types=1);

namespace App\Domains\Account\Login\Handlers;

use App\Shared\Handler;
use App\Domains\Account\Login\LoginContext;
use App\Domains\Account\Login\LoginError;
use Illuminate\Support\Facades\RateLimiter;
use App\Shared\Result;

final class RateLimitHandler extends Handler
{
    /**
     * @phpstan-var string
     */
    private const string RATE_LIMIT_KEY = 'login:';

    /**
     * @phpstan-var int
     */
    private const int ATTEMPTS_PER_MINUTE = 5;

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
        $email = $context->getEmail()->value();
        $key = self::RATE_LIMIT_KEY . md5(string: $email);

        if (RateLimiter::tooManyAttempts(
            key: $key,
            maxAttempts: self::ATTEMPTS_PER_MINUTE
        )) {
            $error = LoginError::TooManyLoginAttempts;

            return Result::failure(error: $error);
        }

        RateLimiter::hit(key: $key, decaySeconds: 60);

        return $next($context);
    }
}
