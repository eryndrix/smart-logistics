<?php declare(strict_types=1);

namespace App\Domains\Message\Handlers;

use App\Shared\Handler;
use App\Domains\Message\MessageContext;
use App\Shared\Enums\Status;
use Illuminate\Database\QueryException;
use App\Models\Message;

/**
 * @phpstan-template TContext
 */
final class MarkAsDeliveredHandler extends Handler
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

        if (!$message instanceof Message) {
            throw new \RuntimeException(
                message: 'Message is not set in context.'
            );
        }

        try {
            /** @phpstan-ignore-next-line */
            $builder = $message->recipients()->where(
                column: 'status',
                operator: '=',
                value: Status::SENT
            );

            $builder->update(['status' => Status::DELIVERED]);
        }

        catch (QueryException $e) {
            throw new \RuntimeException(
                message: 'Failed to mark recipients as delivered.',
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $next($context);
    }
}
