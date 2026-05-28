<?php declare(strict_types=1);

namespace App\Domains\Message;

use App\Shared\Jobs\Job;
use App\Domains\Message\Repositories\Lock\MessageLockRepositoryInterface;
use Illuminate\Support\Facades\{DB, Log};

/**
 * @phpstan-template TData of array<string, mixed>
 */
final class MessageJob extends Job
{
    /**
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        private readonly array $data
    ) {}

    /**
     * @phpstan-param MessageProcess $process
     * @phpstan-return void
     *
     * @throws \Throwable
     */
    public function handle(MessageProcess $process): void
    {
        $command = MessageCommand::fromArray($this->data);
        $context = MessageContext::of(command: $command);

        $fingerprint = $context->getFingerprint();
        
        /** @phpstan-var MessageLockRepositoryInterface<string> */
        $repository = resolve(
            name: MessageLockRepositoryInterface::class
        );

        if ($repository->check(key: $fingerprint)) {
            Log::info(message: 'Skip already processed.', context: [
                'fingerprint' => $fingerprint,
                'payload' => $command->toArray(),
            ]);

            return;
        }

        if (!$repository->acquire(key: $fingerprint)) {
            Log::info(message: 'Skip duplicate.', context: [
                'fingerprint' => $fingerprint,
                'payload' => $command->toArray(),
            ]);

            return;
        }

        try {
            DB::transaction(
                callback: function () use ($process, $context): void {
                    $process->run(payload: $context);
                }
            );

            $repository->mark(key: $fingerprint);
        }

        catch (\Throwable $e) {
            $repository->release(key: $fingerprint);

            Log::error(message: 'MessageJob failed.', context: [
                'fingerprint' => $fingerprint,
                'payload' => $command->toArray(),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
