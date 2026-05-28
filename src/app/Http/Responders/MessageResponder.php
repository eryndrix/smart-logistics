<?php declare(strict_types=1);

namespace App\Http\Responders;

use Illuminate\Http\Response as Status;
use App\Shared\Result;
use App\Http\Responses\ApiResponse;

final class MessageResponder
{
    /**
     * @phpstan-param Result<string, null> $result
     * @phpstan-return ApiResponse
     */
    public function store(Result $result): ApiResponse
    {
        return new ApiResponse(
            data: ['message' => __(key: $result->value)],
            status: Status::HTTP_CREATED
        );
    }
}
