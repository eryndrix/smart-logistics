<?php declare(strict_types=1);

namespace App\Domains\Message\Handlers;

use App\Shared\Handler;
use App\Domains\Message\MessageContext;
use App\Domains\Message\Repositories\MessageRepositoryInterface;
use Illuminate\Database\QueryException;
use App\Models\Message;

/**
 * @phpstan-template TContext
 */
final class PersistMessageHandler extends Handler
{
    /**
     * @phpstan-param MessageRepositoryInterface<Message> $repository
     */
    public function __construct(
        private MessageRepositoryInterface $repository
    ) {}

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
        try {
            $message = new Message([
                'channel' => $context->getChannel(),
                'body' => $context->getBody(),
            ]);

            $this->repository->save(message: $message);
            $context = $context->withMessage(message: $message);
        }

        catch (QueryException $e) {
            throw new \RuntimeException(
                message: 'Failed to persist message.',
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $next($context);
    }
}
