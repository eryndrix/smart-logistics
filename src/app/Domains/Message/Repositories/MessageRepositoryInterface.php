<?php declare(strict_types=1);

namespace App\Domains\Message\Repositories;

use App\Shared\Repositories\MessageRepositoryInterface as RepositoryInterface;
use App\Models\Message;

/**
 * @phpstan-template TModel of Message
 */
interface MessageRepositoryInterface extends RepositoryInterface
{
    /**
     * @phpstan-param TModel $message
     * @phpstan-return void
     */
    public function save(Message $message): void;
}
