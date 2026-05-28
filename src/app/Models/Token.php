<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken;

#[Fillable([
    'tokenable_type',
    'tokenable_id',
    'name',
    'token',
    'abilities',
    'last_used_at',
    'expires_at',
])]
#[Table(name: 'personal_access_tokens', keyType: 'string')]
final class Token extends PersonalAccessToken
{
    /**
     * Use UUIDs as primary keys.
     */
    use HasUuids;

    /**
     * @phpstan-var bool
     */
    public $incrementing = false;

    /**
     * @phpstan-return MorphTo<Model, $this>
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo(
            name: 'tokenable'
        );
    }
}
