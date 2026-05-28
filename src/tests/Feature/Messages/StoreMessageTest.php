<?php declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Models\User;
use App\Domains\Message\MessageJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Feature\ResetsRedis; 

final class StoreMessageTest extends TestCase
{
    use RefreshDatabase, ResetsRedis;

    public function test_it_dispatches_message_job_for_urgent_priority(): void
    {
        Bus::fake();

        $roleId = Str::uuid();
        DB::table('roles')->insert([
            'id' => $roleId,
            'name' => 'admin',
            'slug' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $roleId,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $payload = [
            'channel' => 'sms',
            'message' => 'Hello world!',
            'recipient_ids' => [
                'bd656426-8b4a-43b3-b362-a62ce41916e6',
            ],
        ];

        $response = $this->postJson(
            '/api/v1/messages/urgent',
            $payload,
            [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ]
        );
        
        $response->assertCreated();
        Bus::assertDispatched(MessageJob::class);
    }
}