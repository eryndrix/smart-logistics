<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

final class JournalId extends UniqueId
{
    /**
     * @phpstan-use HasUniqueId<JournalId>
     */
    use HasUniqueId;
}
