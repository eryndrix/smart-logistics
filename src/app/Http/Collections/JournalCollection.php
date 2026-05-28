<?php declare(strict_types=1);

namespace App\Http\Collections;

use App\Http\Resources\JournalResource;

/**
 * @phpstan-extends Collection<JournalResource>
 */
final class JournalCollection extends Collection
{
    /**
     * @phpstan-var string
     */
    public $collects = JournalResource::class;
}
