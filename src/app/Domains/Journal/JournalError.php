<?php declare(strict_types=1);

namespace App\Domains\Journal;

use Illuminate\Http\Response as Status;

enum JournalError: string
{
    case ListFetchFailed = 'list_fetch_failed';

    /**
     * @phpstan-return int
     */
    public function status(): int
    {
        return match ($this) {
            self::ListFetchFailed => Status::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    /**
     * @phpstan-return string
     */
    public function message(): string
    {
        return match ($this) {
            self::ListFetchFailed => 'journal.list_fetch_failed',
        };
    }
}
