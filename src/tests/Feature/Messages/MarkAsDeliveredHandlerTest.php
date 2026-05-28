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

final class MarkAsDeliveredHandlerTest extends TestCase
{
    use RefreshDatabase, ResetsRedis;

    public function test_it_marks_sent_recipients_as_delivered(): void
    {
        $command = MessageCommand::fromArray([
            'channel' => 'mail',
            'message' => 'Hello',
            'recipient_ids' => [
                'e0377d38-5350-47a2-ba42-30f1a4e6c4e6',
                '3d1ed8a0-5bb3-435a-86ea-4ba8ebac119c',
                '337a8917-7fc2-48e0-9913-2563e7131292',
                'bd656426-8b4a-43b3-b362-a62ce41916e6',
            ],
            'priority' => 'normal',
        ]);

        $job = new MessageJob(data: $command->toArray());
        $process = app(MessageProcess::class);

        $job->handle($process);

        $recipient = Recipient::query()->first();

        $this->assertSame(Status::DELIVERED, $recipient->status);
    }
}
