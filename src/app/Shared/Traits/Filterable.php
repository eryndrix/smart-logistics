<?php declare(strict_types=1);

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

// @phpstan-ignore trait.unused
trait Filterable
{
    /**
     * @phpstan-param Builder $query
     * @phpstan-param array $filters
     *
     * @phpstan-return Builder
     */
    protected function scopeFilter(
        Builder $builder,
        array $filters = []): Builder
    {
        return resolve(
            name: Pipeline::class
        )->send(
            passable: $builder
        )->through(
            pipes: $filters
        )->thenReturn();
    }
}
