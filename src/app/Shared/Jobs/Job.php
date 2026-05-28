<?php declare(strict_types=1);

namespace App\Shared\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

abstract class Job implements ShouldQueue, ShouldBeUnique
{
    /**
     * A queued job for asynchronous processing.
     */
    use Queueable;
}
