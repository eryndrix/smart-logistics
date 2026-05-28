<?php declare(strict_types=1);

namespace App\Domains\Journal;

use App\Shared\Handler;
use App\Domains\Journal\Repositories\JournalRepositoryInterface;
use App\Shared\Result;

/**
 * @phpstan-template TQuery of JournalQuery<string>
 * @phpstan-template TSubscriberId of string
 */
final class JournalHandler extends Handler
{
    /**
     * @phpstan-param JournalRepositoryInterface<
     *     \App\Models\Journal
     * > $repository
     */
    public function __construct(
        public JournalRepositoryInterface $repository
    ) {}

    /**
     * @phpstan-param JournalQuery<TSubscriberId> $query
     *
     * @phpstan-return Result<
     *     \Illuminate\Pagination\LengthAwarePaginator<int, mixed>,
     *     JournalError
     * >
     */
    public function handle(JournalQuery $query): Result
    {
        try {
            $journal = $this->repository->findBySubscriberId(
                subscriberId: $query->subscriberId
            );

            return Result::success(value: $journal);
        }

        catch (\Throwable $e) {
            $error = JournalError::ListFetchFailed;
            return Result::failure(error: $error);
        }
    }
}
