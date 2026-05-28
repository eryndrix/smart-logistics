<?php declare(strict_types=1);

namespace App\Domains\Message\Transports;

use Illuminate\Support\ServiceProvider;
use App\Domains\Message\Transports\Mail\Channels\MailChannel;
use App\Domains\Message\Transports\Mail\Channels\MailChannelContract;
use App\Domains\Message\Transports\Sms\Channels\SmsChannel;
use App\Domains\Message\Transports\Sms\Channels\SmsChannelContract;

final class TransportChannelRegistrar extends ServiceProvider
{
    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->app->singleton(
            abstract: MailChannelContract::class,
            concrete: MailChannel::class
        );

        $this->app->singleton(
            abstract: SmsChannelContract::class,
            concrete: SmsChannel::class
        );
    }
}
