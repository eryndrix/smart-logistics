<?php declare(strict_types=1);

namespace App\Domains\Journal\Repositories;

use App\Models\Journal;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @phpstan-implements JournalRepositoryInterface<Journal>
 */
final class JournalRepository implements JournalRepositoryInterface
{
    /**
     * @phpstan-param Journal $journal
     */
    public function __construct(
        private readonly Journal $journal
    ) {}

    /**
     * @phpstan-param string $subscriberId
     * @phpstan-return LengthAwarePaginator<int, mixed>
     */
    public function findBySubscriberId(string $subscriberId): LengthAwarePaginator
    {
        $builder = $this->journal->newQuery();

        /** @phpstan-var \Illuminate\Database\Query\Builder $result */
        $result = $builder->join( // @phpstan-ignore-line
            table: 'message_recipients as mr',
            first: 'message_status_logs.message_recipient_id',
            operator: '=',
            second: 'mr.id'
        )->where(
            column: 'mr.subscriber_id',
            operator: '=',
            value: $subscriberId
        )->select(
            columns: 'message_status_logs.*'
        )->latest(
            column: 'message_status_logs.occurred_at'
        );

        /** @phpstan-var LengthAwarePaginator<int, mixed> */
        return $result->paginate(perPage: 15);
    }
}
