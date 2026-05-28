<?php declare(strict_types=1);

namespace App\Domains\Message;

use App\Shared\Command;
use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\Casting\ArrayCast;

final class MessageCommand extends Command
{
    /**
     * @phpstan-var string
     */
    #[Cast(type: StringCast::class, param: null)]
    public string $channel;

    /**
     * @phpstan-var string
     */
    #[Cast(type: StringCast::class, param: null)]
    public string $body;

    /**
     * @phpstan-var list<string>
     */
    #[Cast(type: ArrayCast::class, param: null)]
    public array $subscriberIds;

    /**
     * @phpstan-var string
     */
    #[Cast(type: StringCast::class, param: null)]
    public string $priority;

    /**
     * @phpstan-return array<string, string>
     */
    protected function mapData(): array
    {
        return [
            'recipient_ids' => 'subscriberIds',
            'message' => 'body',
        ];
    }
}
