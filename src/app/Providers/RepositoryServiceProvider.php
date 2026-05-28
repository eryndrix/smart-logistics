<?php declare(strict_types=1);

namespace App\Providers;

use App\Domains\Account\UserRepository;
use App\Domains\Account\UserRepositoryInterface;
use App\Domains\Journal\Repositories\JournalRepository;
use App\Domains\Journal\Repositories\JournalRepositoryInterface;
use App\Domains\Message\Repositories\Lock\MessageLockRepository;
use App\Domains\Message\Repositories\Lock\MessageLockRepositoryInterface;
use App\Domains\Message\Repositories\MessageRepository;
use App\Domains\Message\Repositories\MessageRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @phpstan-return void
     */
    public function register(): void
    {
        $this->app->bind(
            abstract: UserRepositoryInterface::class,
            concrete: UserRepository::class
        );

        $this->app->bind(
            abstract: MessageRepositoryInterface::class,
            concrete: MessageRepository::class
        );

        $this->app->bind(
            abstract: MessageLockRepositoryInterface::class,
            concrete: MessageLockRepository::class
        );

        $this->app->bind(
            abstract: JournalRepositoryInterface::class,
            concrete: JournalRepository::class
        );
    }
}
