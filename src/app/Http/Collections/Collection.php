<?php declare(strict_types=1);

namespace App\Http\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
 * @template TResource of object
 *
 * @phpstan-property-read \Illuminate\Pagination\LengthAwarePaginator<
 *     int,
 *     TResource
 * > $resource
 * 
 * @phpstan-property-read \Illuminate\Support\Collection<
 *     int,
 *     array<string, mixed>|TResource
 * > $collection
 */
abstract class Collection extends ResourceCollection
{
    /**
     * @phpstan-param Request $request
     *
     * @phpstan-return array{
     *   0?: array<string, mixed>|TResource,
     *   meta: array{
     *     current_page: int,
     *     from: int|null,
     *     last_page: int,
     *     per_page: int,
     *     to: int|null,
     *     total: int
     *   },
     *   links: array{
     *     first: string|null,
     *     last: string|null,
     *     prev: string|null,
     *     next: string|null
     *   }
     * }|non-empty-list<array<string, mixed>|TResource>
     */
    public function toArray(Request $request): array
    {
        $pagination = $this->resource;
        $collection = $this->collection;

        return [
            ...$collection,
            'meta' => [
                'current_page' => $pagination->currentPage(),
                'from' => $pagination->firstItem(),
                'last_page' => $pagination->lastPage(),
                'per_page' => $pagination->perPage(),
                'to' => $pagination->lastItem(),
                'total' => $pagination->total(),
            ],
            'links' => [
                'first' => $pagination->url(page: 1),
                'last' => $pagination->url(
                    page: $pagination->lastPage()
                ),
                'prev' => $pagination->previousPageUrl(),
                'next' => $pagination->nextPageUrl(),
            ],
        ];
    }
}
