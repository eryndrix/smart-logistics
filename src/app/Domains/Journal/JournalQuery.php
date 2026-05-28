<?php declare(strict_types=1);

namespace App\Domains\Journal;

use App\Shared\Query;

/**
 * @phpstan-template TSubscriberId of string
 */
final class JournalQuery extends Query
{
    /**
     * @phpstan-param TSubscriberId $subscriberId
     */
    public function __construct(
        public private(set) string $subscriberId
    ) {}
}
