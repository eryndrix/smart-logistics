<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms;

use App\Shared\ValueObjects\Phone;
use Illuminate\Notifications\Messages\SimpleMessage;
use App\Shared\ValueObjects\Id\MessageId;

/**
 * @template TPhone of Phone
 */
final class SmsMessage extends SimpleMessage
{
    /**
     * @phpstan-var TPhone|null
     */
    public private(set) ?Phone $to = null;

    /**
     * @phpstan-var string|null
     */
    public private(set) ?string $from = null;

    /**
     * @phpstan-var string
     */
    public private(set) string $body = '';

    /**
     * @phpstan-var MessageId|null
     */
    public private(set) ?MessageId $messageId = null;

    /**
     * @phpstan-param TPhone $phone
     * @phpstan-return self<TPhone>
     */
    public function to(Phone $phone): self
    {
        $this->to = $phone;
        return $this;
    }

    /**
     * @phpstan-param string $sender
     * @phpstan-return self<TPhone>
     */
    public function from(string $sender): self
    {
        $this->from = $sender;
        return $this;
    }

    /**
     * @phpstan-param string $body
     * @phpstan-return self<TPhone>
     */
    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @phpstan-param MessageId $messageId
     * @phpstan-return self<TPhone>
     */
    public function messageId(MessageId $messageId): self
    {
        $this->messageId = $messageId;
        return $this;
    }
}
