<?php declare(strict_types=1);

namespace App\Domains\Message\Handlers;

use App\Shared\Handler;
use App\Domains\Message\MessageContext;
use Illuminate\Database\QueryException;
use App\Models\Message;

/**
 * @phpstan-template TContext of MessageContext
 */
final class UpdateStatusHandler extends Handler
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
        $recipients = $context->getRecipients();
        $message = $context->getMessage();

        if (!$message instanceof Message) {
            throw new \RuntimeException(
                message: 'Message is not set in context.'
            );
        }

        try {
            $message->recipients()->upsert(
                values: $recipients,
                uniqueBy: 'id',
                update: ['status', 'error']
            );
        }

        catch (QueryException $e) {
            throw new \RuntimeException(
                message: 'Failed to update recipients status.',
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $next($context);
    }
}
