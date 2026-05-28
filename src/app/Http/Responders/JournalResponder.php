<?php declare(strict_types=1);

namespace App\Http\Responders;

use Illuminate\Http\Response as Status;
use App\Http\Collections\JournalCollection;
use App\Domains\Journal\JournalError;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Responses\ApiResponse;
use App\Shared\Result;

final class JournalResponder
{
    /**
     * @phpstan-param Result<
     *     LengthAwarePaginator<int, \App\Models\Journal>,
     *     JournalError
     * > $result
     * 
     * @phpstan-return ApiResponse
     */
    public function index(Result $result): ApiResponse
    {
        /** @phpstan-var ApiResponse */
        return $result->match(
            onSuccess: fn (LengthAwarePaginator $list)
                => new ApiResponse(
                    data: new JournalCollection(resource: $list),
                    status: Status::HTTP_OK
                ),
            onError: fn(JournalError $error) => new ApiResponse(
                data: ['message' => __(key: $error->message())],
                status: $error->status()
            )
        );
    }
}
