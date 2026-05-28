<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms\Channels;

use App\Domains\Message\Transports\Sms\Providers\SmsProviderContract;
use App\Domains\Message\Transports\Sms\SmsNotificationContract;
use App\Shared\ValueObjects\Phone;

/**
 * @phpstan-template TPhone of Phone
 * @phpstan-template TNotification of SmsNotificationContract<TPhone>
 * @phpstan-implements SmsChannelContract<TPhone, TNotification>
 */
final class SmsChannel implements SmsChannelContract
{
    /**
     * @phpstan-param SmsProviderContract<TPhone> $provider
     */
    public function __construct(
        private readonly SmsProviderContract $provider
    ) {}

    /**
     * @phpstan-param TPhone $phone
     * @phpstan-param TNotification $notification
     *
     * @phpstan-return void
     */
    public function send(Phone $phone, SmsNotificationContract $notification): void
    {
        $smsMessage = $notification->toSms(phone: $phone);
        $this->provider->send(phone: $phone, message: $smsMessage);
    }
}
