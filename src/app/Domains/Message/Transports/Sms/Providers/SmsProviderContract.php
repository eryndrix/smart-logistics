<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms\Providers;

use App\Shared\ValueObjects\Phone;
use App\Domains\Message\Transports\Sms\SmsMessage;

/**
 * @template TPhone of Phone
 */
interface SmsProviderContract
{
    /**
     * @phpstan-param TPhone $phone
     * @phpstan-param SmsMessage<TPhone> $message
     *
     * @phpstan-return void
     */
    public function send(Phone $phone, SmsMessage $message): void;
}
