<?php declare(strict_types=1);

namespace App\Domains\Account;

use App\Models\User;
use App\Shared\ValueObjects\Id\UserId;
use App\Shared\ValueObjects\Email;

/**
 * @phpstan-implements UserRepositoryInterface<User>
 */
final class UserRepository implements UserRepositoryInterface
{
    /**
     * @phpstan-param User $user
     */
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * @phpstan-param UserId $userId
     * @phpstan-return User|null
     */
    public function findById(UserId $userId): ?User
    {
        return $this->user->newQuery()->find(
            id: (string) $userId
        );
    }

    /**
     * @phpstan-param Email $email
     * @phpstan-return User|null
     */
    public function findByEmail(Email $email): ?User
    {
        return $this->user->newQuery()->where(
            column: 'email',
            operator: '=',
            value: $email
        )->first();
    }
}
