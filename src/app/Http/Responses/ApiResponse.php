<?php declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\Response as Status;

final class ApiResponse extends Response
{
    /**
     * @phpstan-param mixed $data
     * @phpstan-param int $status
     */
    public function __construct(
        private readonly mixed $data,
        protected int $status = Status::HTTP_OK
    ) {
        parent::__construct(status: $status);
    }

    /**
     * @phpstan-return array{data: mixed}
     */
    public function data(): array
    {
        return ['data' => $this->data];
    }
}
