<?php declare(strict_types=1);

namespace Tests\Feature\Messages;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Recipient;
use App\Domains\Message\MessageCommand;
use App\Domains\Message\MessageJob;
use App\Domains\Message\MessageProcess;
use App\Shared\Enums\Status;
use Tests\Feature\ResetsRedis; 

final class MessageStatusLogTest extends TestCase
{
    use RefreshDatabase, ResetsRedis;

    public function test_it_writes_status_log_on_update(): void
    {
        $command = MessageCommand::fromArray([
            'channel' => 'sms',
            'message' => 'Hello',
            'recipient_ids' => [
                'e0377d38-5350-47a2-ba42-30f1a4e6c4e6',
                '3ad5fe67-a658-4005-8aa7-30f9aad7afe9',
            ],
            'priority' => 'urgent',
        ]);

        $job = new MessageJob(data: $command->toArray());
        $process = app(MessageProcess::class);

        $job->handle($process);

        $recipient = Recipient::query()->first();

        $recipient->update([
            'status' => Status::DELIVERED->value,
        ]);

        $recipient->update([
            'status' => Status::FAILED->value,
            'error' => 'simulated failure',
        ]);

        $this->assertDatabaseHas('message_status_logs', [
            'message_recipient_id' => (string) $recipient->id,
            'from_status' => Status::DELIVERED->value,
            'to_status' => Status::FAILED->value,
        ]);
    }
}
