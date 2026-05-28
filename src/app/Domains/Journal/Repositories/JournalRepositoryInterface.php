<?php declare(strict_types=1);

namespace App\Domains\Journal\Repositories;

use App\Shared\Repositories\JournalRepositoryInterface as RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @phpstan-template TJournal
 */
interface JournalRepositoryInterface extends RepositoryInterface
{
    /**
     * @phpstan-param string $subscriberId
     * @phpstan-return LengthAwarePaginator<int, mixed>
     */
    public function findBySubscriberId(string $subscriberId): LengthAwarePaginator;
}
