<?php declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @mixin \App\Models\User
 */
final class UserResource extends JsonResource
{
    /**
     * @phpstan-param Request $request
     *
     * @phpstan-return array{
     *   id: string,
     *   name: string,
     *   email: string,
     *   datetime: array{
     *       created_at: string|null,
     *       updated_at: string|null
     *   }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @phpstan-var \App\Models\User $user */
        $user = $this->resource;

        return [
            'id' => $user->id->asString(),
            'name' => $user->name,
            'email' => $user->email->value(),
            'datetime' => [
                'created_at' => $user->createdAt?->format(
                    format: 'Y-m-d H:i:s'
                ),
                'updated_at' => $user->updatedAt?->format(
                    format: 'Y-m-d H:i:s'
                ),
            ],
        ];
    }
}
