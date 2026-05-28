<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

final class UserId extends UniqueId
{
    /**
     * @phpstan-use HasUniqueId<UserId>
     */
    use HasUniqueId;
}
