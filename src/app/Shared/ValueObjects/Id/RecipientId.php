<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

final class RecipientId extends UniqueId
{
    /**
     * @phpstan-use HasUniqueId<RecipientId>
     */
    use HasUniqueId;
}
