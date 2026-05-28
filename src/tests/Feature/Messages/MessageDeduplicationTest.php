<?php declare(strict_types=1);

namespace Tests\Feature\Messages;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Message\MessageCommand;
use App\Domains\Message\MessageJob;
use App\Domains\Message\MessageProcess;
use Tests\Feature\ResetsRedis; 

final class MessageDeduplicationTest extends TestCase
{
    use RefreshDatabase, ResetsRedis;

    public function test_it_skips_duplicate_message_processing(): void
    {
        $command = MessageCommand::fromArray([
            'channel' => 'sms',
            'message' => 'Hello',
            'recipient_ids' => [
                'e0377d38-5350-47a2-ba42-30f1a4e6c4e6',
                '3ad5fe67-a658-4005-8aa7-30f9aad7afe9',
                '337a8917-7fc2-48e0-9913-2563e7131292',
                'bd656426-8b4a-43b3-b362-a62ce41916e6',
            ],
            'priority' => 'urgent',
        ]);

        $job1 = new MessageJob(data: $command->toArray());
        $job2 = new MessageJob(data: $command->toArray());

        $process = app(MessageProcess::class);

        $job1->handle($process);
        $job2->handle($process);

        $this->assertDatabaseCount('messages', 1);
        $this->assertDatabaseCount('message_recipients', 4);
    }
}
