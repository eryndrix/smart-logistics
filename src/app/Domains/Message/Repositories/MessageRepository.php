<?php declare(strict_types=1);

namespace App\Domains\Message\Repositories;

use App\Models\Message;

/**
 * @phpstan-implements MessageRepositoryInterface<Message>
 */
final class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @phpstan-param Message $message
     * @phpstan-return void
     */
    public function save(Message $message): void
    {
        $message->save();
    }
}
