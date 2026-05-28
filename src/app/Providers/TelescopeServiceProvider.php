<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

final class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->hideSensitiveRequestDetails();
        $isLocal = $this->app->environment('local') === 'local';

        Telescope::filter(
            callback: function (IncomingEntry $incomingEntry) use ($isLocal): bool {
                if ($isLocal) {
                    return true;
                }

                if ($incomingEntry->isReportableException()) {
                    return true;
                }

                if ($incomingEntry->isFailedRequest()) {
                    return true;
                }

                if ($incomingEntry->isFailedJob()) {
                    return true;
                }

                if ($incomingEntry->isScheduledTask()) {
                    return true;
                }

                return $incomingEntry->hasMonitoredTag();
            }
        );
    }

    /**
     * @phpstan-return void
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local') === 'local') {
            return;
        }

        Telescope::hideRequestParameters(attributes: ['_token']);
        Telescope::hideRequestHeaders(
            headers: ['cookie', 'x-csrf-token', 'x-xsrf-token']
        );
    }

    /**
     * @phpstan-return void
     */
    protected function gate(): void
    {
        Gate::define(
            ability: 'viewTelescope',
            callback: function (User $user): bool {
                return true;
            }
        );
    }
}
