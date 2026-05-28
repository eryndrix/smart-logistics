<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

final class RoleId extends UniqueId
{
    /**
     * @phpstan-use HasUniqueId<RoleId>
     */
    use HasUniqueId;
}
