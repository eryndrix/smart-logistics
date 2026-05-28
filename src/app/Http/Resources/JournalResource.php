<?php declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @mixin \App\Models\Journal
 */
final class JournalResource extends JsonResource
{
    /**
     * @phpstan-param Request $request
     *
     * @phpstan-return array{
     *     id: non-empty-string,
     *     message_recipient_id: non-empty-string,
     *     from_status: string|null,
     *     to_status: string,
     *     reason: string|null,
     *     occurred_at: non-falsy-string|null,
     *     recipient?: array{
     *         id: non-empty-string,
     *         message_id: non-empty-string,
     *         subscriber_id: string,
     *         address: string,
     *         status: \App\Shared\Enums\Status,
     *         error: string|null,
     *         datetime: array{
     *             created_at: non-falsy-string|null,
     *             updated_at: non-falsy-string|null
     *         }
     *     }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @phpstan-var \App\Models\Journal $journal */
        $journal = $this->resource;

        $data = [
            'id' => (string) $journal->id,
            'message_recipient_id' => (string) $journal->messageRecipientId,
            'from_status' => $journal->fromStatus,
            'to_status' => $journal->toStatus,
            'reason' => $journal->reason,
            'occurred_at' => $journal->occurredAt?->format(
                format: 'Y-m-d H:i:s'
            ),
        ];

        if ($journal->relationLoaded(key: 'recipient')) {
            /**
             * @phpstan-var \Illuminate\Database\Eloquent\Relations\BelongsTo<
             *     \App\Models\Recipient,
             *     \App\Models\Journal
             * > $relation */
            $relation = $journal->recipient();

            /** @phpstan-var \App\Models\Recipient $recipient */
            $recipient = $relation->getResults();

            $data['recipient'] = [
                'id' => (string) $recipient->id,
                'message_id' => (string) $recipient->messageId,
                'subscriber_id' => $recipient->subscriberId,
                'address' => $recipient->address,
                'status' => $recipient->status,
                'error' => $recipient->error,
                'datetime' => [
                    'created_at' => $recipient->createdAt?->format(
                        format: 'Y-m-d H:i:s'
                    ),
                    'updated_at' => $recipient->updatedAt?->format(
                        format: 'Y-m-d H:i:s'
                    ),
                ],
            ];
        }

        return $data;
    }
}
