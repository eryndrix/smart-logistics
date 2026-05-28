<?php declare(strict_types=1);

namespace App\Shared\Contracts;

interface SubscriberContract
{
    /**
     * @phpstan-return array<string, mixed>
     */
    public function findById(string $id): array;
}
