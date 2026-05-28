<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms;

use App\Shared\ValueObjects\Phone;

/**
 * @template TPhone of Phone
 */
interface SmsNotificationContract
{
    /**
     * @phpstan-param TPhone $phone
     * @phpstan-return SmsMessage<TPhone>
     */
    public function toSms(Phone $phone): SmsMessage;
}
