<?php declare(strict_types=1);

namespace App\Domains\Message;

use App\Shared\Context;
use App\Shared\Enums\Channel;
use App\Shared\Enums\Priority;
use App\Models\Message;

final class MessageContext extends Context
{
    /**
     * @phpstan-param MessageCommand $command
     * @phpstan-param list<array<string, mixed>> $recipients
     * @phpstan-param Message|null $message
     */
    public function __construct(
        private MessageCommand $command,
        private array $recipients = [],
        private ?Message $message = null
    ) {}

    /**
     * @phpstan-param MessageCommand $command
     * @phpstan-return self
     */
    public static function of(MessageCommand $command): self
    {
        return new self(command: $command);
    }

    /**
     * @phpstan-return Channel
     * @throws \InvalidArgumentException
     */
    public function getChannel(): Channel
    {
        $channel = Channel::tryFrom(value: $this->command->channel);

        if ($channel === null) {
            throw new \InvalidArgumentException(
                message: 'Invalid channel: ' . $this->command->channel
            );
        }

        return $channel;
    }

    /**
     * @phpstan-return string
     */
    public function getBody(): string
    {
        $body = strip_tags(string: $this->command->body);
        return trim(string: $body);
    }

    /**
     * @phpstan-return list<string>
     */
    public function getSubscriberIds(): array
    {
        /** @phpstan-var list<string> */
        return $this->command->subscriberIds ?? [];
    }

    /**
     * @phpstan-return Priority
     * @throws \InvalidArgumentException
     */
    public function getPriority(): Priority
    {
        $priority = Priority::tryFrom(value: $this->command->priority);

        if ($priority === null) {
            throw new \InvalidArgumentException(
                message: 'Invalid priority: ' . $this->command->priority
            );
        }

        return $priority;
    }

    /**
     * @phpstan-return string
     */
    public function getFingerprint(): string
    {
        $subscriberIds = $this->getSubscriberIds();
        sort(array: $subscriberIds);

        return hash(algo: 'sha256', data: implode(
            separator: '|',
            array: [
                $this->getChannel()->value,
                $this->getBody(),
                (string) $this->getPriority()->value,
                implode(
                    separator: ',',
                    array: $subscriberIds
                ),
            ]
        ));
    }

    /**
     * @phpstan-param list<array<string, mixed>> $recipients
     * @phpstan-return self
     */
    public function setRecipients(array $recipients): self
    {
        $clone = clone $this;
        $clone->recipients = $recipients;

        return $clone;
    }

    /**
     * @phpstan-return list<array<string, mixed>>
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @phpstan-param Message $message
     * @phpstan-return self
     */
    public function withMessage(Message $message): self
    {
        $clone = clone $this;
        $clone->message = $message;

        return $clone;
    }

    /**
     * @phpstan-return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }
}
