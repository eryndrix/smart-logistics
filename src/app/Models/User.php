<?php declare(strict_types=1);

namespace App\Models;

use App\Shared\Traits\CamelCaseAttributes;
use App\Shared\ValueObjects\Email;
use App\Shared\ValueObjects\Id\UserId;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @phpstan-property UserId $id
 * @phpstan-property string $name
 * @phpstan-property Email $email
 * @phpstan-property string $password
 * @phpstan-property Carbon|null $createdAt
 * @phpstan-property Carbon|null $updatedAt
 */
#[Fillable(['name', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'remember_token',])]
#[Table(name: 'users', keyType: 'string')]
final class User extends Authenticatable
{
    /**
     * Use UUIDs for primary keys.
     */
    use HasUuids;

    /**
     * Enables API token authentication.
     */
    use HasApiTokens;

    /**
     * @phpstan-use CamelCaseAttributes<self>
     */
    use CamelCaseAttributes;

    /**
     * Enables notification support.
     */
    use Notifiable;

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
            'id' => UserId::class,
            'email' => Email::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @phpstan-return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(
            related: Role::class,
            foreignKey: 'role_id',
            ownerKey: 'id'
        );
    }
}
