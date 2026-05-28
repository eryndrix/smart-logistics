<?php declare(strict_types=1);

namespace App\Domains\Account\CheckMe;

use App\Models\User;
use App\Shared\Query;

final class CheckMeQuery extends Query
{
    /**
     * @phpstan-param User|null $user
     */
    public function __construct(
        public private(set) ?User $user
    ) {}
}
