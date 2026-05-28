<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail;

use App\Shared\ValueObjects\Email;
use Illuminate\Notifications\Messages\SimpleMessage;
use App\Shared\ValueObjects\Id\MessageId;

/**
 * @phpstan-template TEmail of Email
 */
final class MailMessage extends SimpleMessage
{
    /**
     * @phpstan-var TEmail|null
     */
    public private(set) ?Email $to = null;

    /**
     * @phpstan-var array{address: Email, name: string|null}|null
     */
    public private(set) ?array $from = null;

    /**
     * @phpstan-var array{address: Email, name: string|null}|null
     */
    public private(set) ?array $replyTo = null;

    /**
     * @phpstan-var MessageId|null
     */
    public private(set) ?MessageId $messageId = null;

    /**
     * @phpstan-var string
     */
    public private(set) string $body = '';

    /**
     * @phpstan-param TEmail $email
     * @phpstan-return self<TEmail>
     */
    public function to(Email $email): self
    {
        $this->to = $email;

        return $this;
    }

    /**
     * @phpstan-param non-empty-string $address
     * @phpstan-param string|null $name
     *
     * @phpstan-return self<TEmail>
     */
    public function from(
        string $address, ?string $name = null): self
    {
        $this->from = [
            'address' => $this->make(value: $address),
            'name' => $name,
        ];

        return $this;
    }

    /**
     * @phpstan-param non-empty-string $address
     * @phpstan-param string|null $name
     *
     * @phpstan-return self<TEmail>
     */
    public function replyTo(
        string $address, ?string $name = null): self
    {
        $this->replyTo = [
            'address' => $this->make(value: $address),
            'name' => $name,
        ];

        return $this;
    }

    /**
     * @phpstan-param string $value
     * @phpstan-return Email
     * 
     * @throws \InvalidArgumentException
     */
    private function make(string $value): Email
    {
        $value = trim(string: $value);

        if ($value === '') {
            throw new \InvalidArgumentException(
                message: 'Email address must not be empty.'
            );
        }

        return Email::of(value: $value);
    }

    /**
     * @phpstan-param string $body
     * @phpstan-return self<TEmail>
     */
    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @phpstan-param MessageId $messageId
     * @phpstan-return self<TEmail>
     */
    public function messageId(MessageId $messageId): self
    {
        $this->messageId = $messageId;
        return $this;
    }
}
