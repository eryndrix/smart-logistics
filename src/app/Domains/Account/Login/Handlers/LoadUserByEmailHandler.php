<?php declare(strict_types=1);

namespace App\Domains\Account\Login\Handlers;

use App\Shared\Handler;
use App\Domains\Account\Login\LoginContext;
use App\Models\User;
use App\Domains\Account\UserRepositoryInterface;
use App\Domains\Account\Login\LoginError;
use App\Shared\Result;

final class LoadUserByEmailHandler extends Handler
{
    /**
     * @phpstan-param UserRepositoryInterface<User> $repository
     */
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

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
        $user = $this->repository->findByEmail(
            email: $context->getEmail()
        );

        if (!$user instanceof User) {
            $error = LoginError::UserNotFound;
            return Result::failure(error: $error);
        }

        $context = $context->withUser(user: $user);

        return $next($context);
    }
}
