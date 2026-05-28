<?php declare(strict_types=1);

namespace App\External\Integrations;

use App\Shared\Contracts\SubscriberContract;
use Illuminate\Support\Facades\Redis;

final class SubscriberCache implements SubscriberContract
{
    /**
     * @phpstan-var string
     */
    private const string ITEM_KEY = 'subscriber:%s';

    /**
     * @phpstan-param string $id
     *
     * @phpstan-return array<string, mixed>
     */
    public function findById(string $id): array
    {
        $key = sprintf(self::ITEM_KEY, $id);
        // @phpstan-ignore-next-line
        $data = Redis::connection()->get(key: $key);

        if ($data === null) {
            return [];
        }

        /** @phpstan-var string $data */
        $result = json_decode(json: $data, associative: true);

        if (!is_array(value: $result)) {
            return [];
        }

        /** @phpstan-var array<string, mixed> $result */
        return $result;
    }

    /**
     * @phpstan-param array<array<string, mixed>> $items
     *
     * @phpstan-return void
     */
    public function store(array $items): void
    {
        // @phpstan-ignore-next-line
        Redis::connection()->pipeline(
            function (\Redis $redis) use ($items): void {
                /** @phpstan-var array<string, mixed> $data */
                $data = [];

                foreach ($items as $item) {
                    if (!isset($item['id'])) {
                        continue;
                    }

                    if (!is_string(value: $item['id'])) {
                        continue;
                    }

                    $data[] = $item;

                    $key = sprintf(self::ITEM_KEY, $item['id']);
                    $value = json_encode(value: $item);

                    $redis->setnx(key: $key, value: $value);
                }
            }
        );
    }
}
