<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

final class MessageId extends UniqueId
{
    /**
     * @phpstan-use HasUniqueId<MessageId>
     */
    use HasUniqueId;
}
