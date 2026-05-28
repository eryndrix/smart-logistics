<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Shared\Traits\CamelCaseAttributes;
use App\Shared\ValueObjects\Id\JournalId;
use App\Shared\ValueObjects\Id\RecipientId;

/**
 * @phpstan-property JournalId $id
 * @phpstan-property RecipientId $messageRecipientId
 * @phpstan-property string|null $fromStatus
 * @phpstan-property string $toStatus
 * @phpstan-property string|null $reason
 * @phpstan-property \Illuminate\Support\Carbon|null $occurredAt
 */
#[Fillable([
    'message_recipient_id',
    'from_status',
    'to_status',
    'reason',
])]
#[Table(name: 'message_status_logs', keyType: 'string')]
final class Journal extends Model
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
     * @phpstan-var bool
     */
    public $timestamps = false;

    /**
     * @phpstan-return array<string, class-string|non-empty-string>
     */
    protected function casts(): array
    {
        return [
            'id' => JournalId::class,
            'message_recipient_id' => RecipientId::class,
            'occurred_at' => 'datetime:U',
        ];
    }

    /**
     * @phpstan-return BelongsTo<Recipient, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(
            related: Recipient::class,
            foreignKey: 'message_recipient_id',
            ownerKey: 'id'
        );
    }
}
