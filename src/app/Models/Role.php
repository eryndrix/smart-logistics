<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Shared\Traits\CamelCaseAttributes;
use App\Shared\ValueObjects\Id\RoleId;
use App\Shared\ValueObjects\Slug\RoleSlug;

#[Fillable(['name', 'slug'])]
#[Table(name: 'roles', keyType: 'string')]
final class Role extends Model
{
    /**
     * Use UUIDs for primary keys.
     */
    use HasUuids;

    /**
     * @phpstan-use CamelCaseAttributes<self>
     */
    use CamelCaseAttributes;

    /**
     * @phpstan-var bool
     */
    public $incrementing = false;

    /**
     * @phpstan-return array<string, class-string|non-empty-string>
     */
    protected function casts(): array
    {
        return [
            'id' => RoleId::class,
            'slug' => RoleSlug::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @phpstan-return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(
            related: User::class,
            foreignKey: 'role_id',
            localKey: 'id'
        );
    }
}
