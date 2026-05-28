<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms\Channels;

use App\Domains\Message\Transports\Sms\SmsNotificationContract;
use App\Shared\ValueObjects\Phone;

/**
 * @phpstan-template TPhone of Phone
 * @phpstan-template TNotification of SmsNotificationContract<TPhone>
 */
interface SmsChannelContract
{
    /**
     * @phpstan-param TPhone $phone
     * @phpstan-param TNotification $notification
     *
     * @phpstan-return void
     */
    public function send(Phone $phone, SmsNotificationContract $notification): void;
}
