<?php declare(strict_types=1);

namespace App\Domains\Message\Handlers;

use App\Shared\Handler;
use App\Domains\Message\MessageContext;
use App\Shared\Enums\Channel;
use App\Shared\Enums\Status;
use Illuminate\Database\QueryException;
use App\Models\Message;

/**
 * @phpstan-template TContext
 */
final class PersistRecipientsHandler extends Handler
{
    /**
     * @phpstan-param MessageContext $context
     * @phpstan-param \Closure $next
     *
     * @phpstan-return mixed
     * 
     * @throws \RuntimeException
     */
    public function handle(
        MessageContext $context, \Closure $next): mixed
    {
        $message = $context->getMessage();
        $channel = $context->getChannel();

        if (!$message instanceof Message) {
            throw new \RuntimeException(
                message: 'Message is not set in context.'
            );
        }

        $recipients = [];

        foreach ($context->getRecipients() as $recipient) {
            $recipients[] = [
                'subscriber_id' => $recipient['id'],
                'address' => match ($channel) {
                    Channel::SMS => $recipient['phone'],
                    Channel::MAIL => $recipient['email'],
                },
                'status' => Status::QUEUED,
            ];
        }

        try {
            $message->recipients()->createMany(
                records: $recipients
            );
        } catch (QueryException $e) {
            throw new \RuntimeException(
                message: 'Failed to persist recipients.',
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $next($context);
    }
}
