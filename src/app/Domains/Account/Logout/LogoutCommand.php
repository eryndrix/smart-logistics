<?php declare(strict_types=1);

namespace App\Domains\Account\Logout;

use App\Models\User;
use App\Shared\Command;

final class LogoutCommand extends Command
{
    /**
     * @phpstan-param User $user
     */
    public function __construct(
        public private(set) User $user
    ) {}
}
