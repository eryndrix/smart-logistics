<?php declare(strict_types=1);

namespace App\Domains\Account;

use App\Shared\ValueObjects\Email;
use App\Shared\Repositories\UserRepositoryInterface as RepositoryInterface;
use App\Shared\ValueObjects\Id\UserId;
use App\Models\User;

/**
 * @phpstan-template TModel of User
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @phpstan-param UserId $userId
     * @phpstan-return User|null
     */
    public function findById(UserId $userId): ?User;

    /**
     * @phpstan-param Email $email
     * @phpstan-return User|null
     */
    public function findByEmail(Email $email): ?User;
}
