<?php declare(strict_types=1);

namespace App\Shared\Jobs;

use Illuminate\Support\Facades\Bus;

abstract class JobChain
{
    /**
     * @phpstan-var array<int, object|string|callable>
     */
    protected array $jobs = [];

    /**
     * @phpstan-return mixed
     */
    public function run(): mixed
    {
        return Bus::chain(jobs: $this->jobs)->dispatch();
    }
}
