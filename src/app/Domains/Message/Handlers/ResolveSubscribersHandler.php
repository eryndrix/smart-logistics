<?php declare(strict_types=1);

namespace App\Domains\Message\Handlers;

use App\Domains\Message\MessageContext;
use App\Shared\Contracts\SubscriberContract;
use App\Shared\Handler;

/**
 * @phpstan-template TContext of MessageContext
 */
final class ResolveSubscribersHandler extends Handler
{
    /**
     * @phpstan-param SubscriberContract $subscriber
     */
    public function __construct(
        private SubscriberContract $subscriber
    ) {}

    /**
     * @phpstan-param MessageContext $context
     * @phpstan-param \Closure $next
     *
     * @phpstan-return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function handle(
        MessageContext $context, \Closure $next): mixed
    {
        /** @phpstan-var list<string> $subscriberIds */
        $subscriberIds = array_unique(
            array: $context->getSubscriberIds()
        );

        if ($subscriberIds === []) {
            throw new \InvalidArgumentException(
                message: 'Subscriber IDs list cannot be empty.'
            );
        }

        /** @phpstan-var list<array<string, mixed>> $recipients */
        $recipients = [];

        foreach ($subscriberIds as $subscriberId) {
            try {
                /** @phpstan-var array<string, mixed> $subscriber */
                $subscriber = $this->subscriber->findById(
                    id: $subscriberId
                );

                if ($subscriber === null) {
                    continue;
                }

                $recipients[] = $subscriber;
            }

            catch (\Throwable $e) {
                throw new \RuntimeException(
                    message: sprintf(
                        'Failed to resolve subscriber with id: %s.',
                        $subscriberId
                    ),
                    code: $e->getCode(),
                    previous: $e
                );
            }
        }

        if ($recipients === []) {
            throw new \InvalidArgumentException(
                message: 'No valid subscribers found from the provided ids.'
            );
        }

        $context = $context->setRecipients(recipients: $recipients);

        return $next($context);
    }
}
