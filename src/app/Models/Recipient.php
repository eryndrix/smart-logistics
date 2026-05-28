<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Shared\Traits\CamelCaseAttributes;
use App\Shared\ValueObjects\Id\MessageId;
use App\Shared\ValueObjects\Id\RecipientId;
use App\Shared\Enums\Status;

/**
 * @phpstan-property RecipientId $id
 * @phpstan-property MessageId $messageId
 * @phpstan-property string $subscriberId
 * @phpstan-property string $address
 * @phpstan-property Status $status
 * @phpstan-property string|null $error
 * @phpstan-property \Illuminate\Support\Carbon|null $createdAt
 * @phpstan-property \Illuminate\Support\Carbon|null $updatedAt
 */
#[Fillable([
    'message_id',
    'subscriber_id',
    'address',
    'status',
    'error',
])]
#[Table(name: 'message_recipients', keyType: 'string')]
final class Recipient extends Model
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
            'id' => RecipientId::class,
            'message_id' => MessageId::class,
            'status' => Status::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @phpstan-return BelongsTo<Message, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(
            related: Message::class,
            foreignKey: 'message_id',
            ownerKey: 'id'
        );
    }

    /**
     * @phpstan-return HasMany<Journal, $this>
     */
    public function journals(): HasMany
    {
        return $this->hasMany(
            related: Journal::class,
            foreignKey: 'message_recipient_id',
            localKey: 'id'
        );
    }
}
