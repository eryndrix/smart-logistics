<?php declare(strict_types=1);

namespace App\Domains\Message\Transports;

use Illuminate\Support\ServiceProvider;
use App\Domains\Message\Transports\Mail\Providers\MailProvider;
use App\Domains\Message\Transports\Mail\Providers\MailProviderContract;
use App\Domains\Message\Transports\Sms\Providers\SmsProvider;
use App\Domains\Message\Transports\Sms\Providers\SmsProviderContract;

final class TransportProviderRegistrar extends ServiceProvider
{
    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->app->bind(
            abstract: MailProviderContract::class,
            concrete: MailProvider::class
        );

        $this->app->bind(
            abstract: SmsProviderContract::class,
            concrete: SmsProvider::class
        );
    }
}
