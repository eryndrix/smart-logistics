<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;

trait ResetsRedis
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetRedis();
    }

    protected function resetRedis(): void
    {
        Redis::flushdb();
    }
}
