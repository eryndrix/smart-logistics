<?php declare(strict_types=1);

namespace App\Domains\Message;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Domains\Message\Transports\Mail\MailMessage;
use App\Domains\Message\Transports\Mail\MailNotificationContract;
use App\Domains\Message\Transports\Mail\Channels\MailChannel;
use App\Domains\Message\Transports\Sms\SmsMessage;
use App\Domains\Message\Transports\Sms\SmsNotificationContract;
use App\Domains\Message\Transports\Sms\Channels\SmsChannel;
use App\Shared\Enums\Channel;
use App\Shared\ValueObjects\Email;
use App\Shared\ValueObjects\Id\MessageId;
use App\Shared\ValueObjects\Phone;

/**
 * @phpstan-implements MailNotificationContract<Email>
 * @phpstan-implements SmsNotificationContract<Phone>
 */
final class MessageNotification extends Notification implements
    ShouldQueue,
    MailNotificationContract,
    SmsNotificationContract
{
    use Queueable;

    /**
     * @phpstan-param Channel $channel
     * @phpstan-param string $body
     */
    public function __construct(
        private readonly Channel $channel,
        private readonly string $body,
        private readonly MessageId $messageId
    ) {}

    /**
     * @phpstan-param object $notifiable
     * @phpstan-return list<class-string>
     */
    public function via(object $notifiable): array
    {
        return match ($this->channel) {
            Channel::MAIL => [MailChannel::class],
            Channel::SMS => [SmsChannel::class]
        };
    }

    /**
     * @phpstan-param Email $email
     * @phpstan-return MailMessage<Email>
     * 
     * @throws \RuntimeException
     */
    public function toMail(Email $email): MailMessage
    {
        $fromAddress = config(key: 'mail.from.address');
        $fromName = config(key: 'mail.from.name');
        $replyToAddress = config(key: 'mail.reply_to.address');
        $replyToName = config(key: 'mail.reply_to.name');

        if (!is_string(value: $fromAddress)
            || $fromAddress === ''
        ) {
            throw new \RuntimeException(
                message: 'Mail from address is not configured.'
            );
        }

        if (!is_string(value: $replyToAddress)
            || $replyToAddress === ''
        ) {
            throw new \RuntimeException(
                message: 'Mail reply-to address is not configured.'
            );
        }

        return new MailMessage()
            ->from(
                address: $fromAddress,
                name: is_string(value: $fromName)
                    ? $fromName
                    : null
            )
            ->replyTo(
                address: $replyToAddress,
                name: is_string(value: $replyToName)
                    ? $replyToName
                    : null
            )
            ->to(email: $email)
            ->body(body: $this->body)
            ->messageId(
                messageId: $this->messageId
            );
    }

    /**
     * @phpstan-param Phone $phone
     * @phpstan-return SmsMessage<Phone>
     * 
     * @throws \RuntimeException
     */
    public function toSms(Phone $phone): SmsMessage
    {
        $sender = config(key: 'services.sms.sender');

        if (!is_string(value: $sender) || $sender === '') {
            throw new \RuntimeException(
                message: 'SMS sender is not configured.'
            );
        }

        return new SmsMessage()
            ->from(sender: $sender)
            ->to(phone: $phone)
            ->body(body: $this->body)
            ->messageId(
                messageId: $this->messageId
            );
    }
}
