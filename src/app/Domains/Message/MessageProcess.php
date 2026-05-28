<?php declare(strict_types=1);

namespace App\Domains\Message;

use App\Shared\Process;
use App\Domains\Message\Handlers\MarkAsDeliveredHandler;
use App\Domains\Message\Handlers\PersistMessageHandler;
use App\Domains\Message\Handlers\PersistRecipientsHandler;
use App\Domains\Message\Handlers\ResolveSubscribersHandler;
use App\Domains\Message\Handlers\SendMessageHandler;
use App\Domains\Message\Handlers\UpdateStatusHandler;
use App\Shared\Enums\Priority;
use App\Shared\Result;

/**
 * @extends Process<MessageContext, Result<string, string>>
 */
final class MessageProcess extends Process
{
    /**
     * @phpstan-var list<class-string>
     */
    protected array $handlers = [
        ResolveSubscribersHandler::class,
        PersistMessageHandler::class,
        PersistRecipientsHandler::class,
        SendMessageHandler::class,
        UpdateStatusHandler::class,
        MarkAsDeliveredHandler::class,
    ];

    /**
     * @phpstan-param MessageCommand $command
     * @phpstan-return Result<string, string>
     */
    public function __invoke(MessageCommand $command): Result
    {
        $data = $command->toArray();
        /** @phpstan-var array<string, mixed> $data */
        $messageJob = new MessageJob(data: $data);

        $priority = Priority::tryFrom(
            value: $command->priority
        );

        if ($priority === null) {
            return Result::failure(
                error: 'Invalid priority value'
            );
        }

        dispatch(
            $messageJob->onConnection(
                connection: 'rabbitmq'
            )->onQueue(
                queue: $priority->worker()
            )
        );

        return Result::success(
            value: 'message.sent_successfully'
        );
    }
}
