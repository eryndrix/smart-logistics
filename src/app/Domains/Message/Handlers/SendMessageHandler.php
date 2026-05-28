<?php declare(strict_types=1);

namespace App\Domains\Message\Handlers;

use App\Shared\Handler;
use App\Domains\Message\MessageContext;
use App\Domains\Message\MessageNotification;
use App\Domains\Message\Transports\Mail\Channels\MailChannelContract;
use App\Domains\Message\Transports\Sms\Channels\SmsChannelContract;
use App\Shared\Enums\Channel;
use App\Shared\Enums\Status;
use App\Shared\ValueObjects\Email;
use App\Shared\ValueObjects\Phone;
use App\Models\Message;

/**
 * @phpstan-template TContext of MessageContext
 */
final class SendMessageHandler extends Handler
{
    /**
     * @phpstan-param MailChannelContract<
     *     Email,
     *     MessageNotification
     * > $mailChannel
     * 
     * @phpstan-param SmsChannelContract<
     *     Phone,
     *     MessageNotification
     * > $smsChannel
     */
    public function __construct(
        private readonly MailChannelContract $mailChannel,
        private readonly SmsChannelContract $smsChannel
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
        $message = $context->getMessage();

        if (!$message instanceof Message) {
            throw new \RuntimeException(
                message: 'Message is not set in context.'
            );
        }

        $notification = new MessageNotification(
            channel: $context->getChannel(),
            body: $context->getBody(),
            messageId: $message->id
        );

        $recipients = $message->recipients()->get();

        /**
         * @phpstan-var list<array{
         *     id: string,
         *     subscriber_id: string,
         *     address: string,
         *     status: Status,
         *     error?: string
         * }>
         */
        $result = [];

        foreach ($recipients as $recipient) {
            /** @phpstan-var string $address */
            $address = $recipient->address;

            if ($address === '') {
                throw new \RuntimeException(
                    'Recipient address is empty'
                );
            }

            $data = [
                'id' => (string) $recipient->id,
                'subscriber_id' => $recipient->subscriberId,
                'address' => $address,
            ];

            try {
                match ($context->getChannel()) {
                    Channel::MAIL => $this->mailChannel->send(
                        email: Email::of(value: $address),
                        notification: $notification
                    ),
                    Channel::SMS => $this->smsChannel->send(
                        phone: Phone::of(value: $address),
                        notification: $notification
                    ),
                };

                $result[] = [...$data, 'status' => Status::SENT];
            }

            catch (\Throwable $e) {
                $result[] = [
                    ...$data,
                    'status' => Status::FAILED,
                    'error' => $e->getMessage(),
                ];

                continue;
            }
        }

        $context = $context->setRecipients(recipients: $result);

        return $next($context);
    }
}
