<?php declare(strict_types=1);

namespace App\Shared;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * @phpstan-extends LengthAwarePaginator<int, mixed>
 */
final class Paginator extends LengthAwarePaginator
{
    /**
     * @phpstan-param array<int, mixed> $items
     * @phpstan-param int $perPage
     * @phpstan-param Request|null $request
     */
    public function __construct(
        array $items,
        int $perPage = 10,
        ?Request $request = null
    ) {
        $request ??= request();
        $currentPage = max(
            1,
            (int) $request->query(key: 'page', default: '1')
        );

        parent::__construct(
            items: $this->fromArray(
                items: $items,
                perPage: $perPage,
                currentPage: $currentPage
            ),
            total: count(value: $items),
            perPage: $perPage,
            currentPage: $currentPage,
            options: [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    /**
     * @phpstan-param array<int, mixed> $items
     * @phpstan-param int $perPage
     * @phpstan-param int $currentPage
     *
     * @phpstan-return array<int, mixed>
     */
    private function fromArray(
        array $items,
        int $perPage,
        int $currentPage
    ): array {
        return array_slice(
            array: $items,
            offset: ($currentPage - 1) * $perPage,
            length: $perPage
        );
    }
}
