<?php declare(strict_types=1);

namespace App\Domains\Account\Login;

use App\Shared\Context;
use App\Shared\ValueObjects\Email;
use App\Models\User;

final class LoginContext extends Context
{
    /**
     * @phpstan-param LoginCommand $command
     * @phpstan-param User|null $user
     */
    public function __construct(
        private LoginCommand $command,
        private ?User $user = null
    ) {}

    /**
     * @phpstan-param LoginCommand $command
     * @phpstan-return self
     */
    public static function of(LoginCommand $command): self
    {
        return new self(command: $command);
    }

    /**
     * @phpstan-return Email
     */
    public function getEmail(): Email
    {
        /** @phpstan-var non-empty-string $email */
        $email = $this->command->email;

        return Email::of(value: $email);
    }

    /**
     * @phpstan-return string
     */
    public function getPassword(): string
    {
        return $this->command->password;
    }

    /**
     * @phpstan-return bool
     */
    public function getRememberMe(): bool
    {
        return $this->command->rememberMe;
    }

    /**
     * @phpstan-param User $user
     * @phpstan-return static
     */
    public function withUser(User $user): self
    {
        $clone = clone $this;
        $clone->user = $user;

        return $clone;
    }

    /**
     * @phpstan-return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
