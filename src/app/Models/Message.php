<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Shared\Traits\CamelCaseAttributes;
use App\Shared\ValueObjects\Id\MessageId;
use App\Shared\Enums\Channel;

/**
 * @phpstan-property MessageId $id
 */
#[Fillable(['channel', 'body'])]
#[Table(name: 'messages', keyType: 'string')]
final class Message extends Model
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
            'id' => MessageId::class,
            'channel' => Channel::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @phpstan-return HasMany<Recipient, $this>
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(
            related: Recipient::class,
            foreignKey: 'message_id',
            localKey: 'id'
        );
    }
}
