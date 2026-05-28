<?php declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;

abstract class Response implements Responsable
{
    /**
     * @phpstan-param int $status
     */
    protected function __construct(
        protected int $status
    ) {}

    /**
     * @phpstan-return array<string, mixed>
     */
    abstract public function data(): array;

    /**
     * @phpstan-param mixed $request
     * @phpstan-return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        $requestId = Context::get(key: 'request_id');
        $timestamp = Context::get(key: 'timestamp');

        $data = ['status' => $this->status]
            + $this->data()
            + [
                'metadata' => [
                    'request_id' => $requestId,
                    'timestamp' => $timestamp,
                ],
            ];

        return new JsonResponse(
            data: $data,
            status: $this->status
        );
    }
}
