<?php declare(strict_types=1);

namespace App\Shared\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use App\Shared\Casts\EmailCast;

final class Email implements Castable
{
    /**
     * @phpstan-var non-empty-string
     */
    private string $email;

    /**
     * @phpstan-param non-empty-string $email
     * @throws \InvalidArgumentException
     */
    public function __construct(string $email)
    {
        $this->ensureValidEmail(email: $email);

        $normalizedEmail = mb_strtolower(
            string: trim(string: $email)
        );

        if ($normalizedEmail === '') {
            throw new \InvalidArgumentException(
                message: 'Email cannot be empty after normalization.'
            );
        }

        /** @phpstan-var lowercase-string&non-empty-string $normalizedEmail */
        $this->email = $normalizedEmail;
    }

    /**
     * @phpstan-param non-empty-string $email
     * @phpstan-return void
     * 
     * @throws \InvalidArgumentException
     */
    private function ensureValidEmail(string $email): void
    {
        if (!(bool) filter_var(
            value: $email,
            filter: FILTER_VALIDATE_EMAIL
        )) {
            throw new \InvalidArgumentException(
                message: sprintf(
                    'Invalid Email Format: "%s"',
                    $email
                )
            );
        }

        $this->ensureMaxLength(email: $email);
    }

    /**
     * @phpstan-param non-empty-string $email
     * @phpstan-return void
     */
    private function ensureMaxLength(string $email): void
    {
        if (mb_strlen(string: $email, encoding: 'UTF-8') > 254) {
            throw new \InvalidArgumentException(
                message: sprintf(
                    'Email length exceeds 254 characters: "%s"',
                    $email
                )
            );
        }
    }

    /**
     * @phpstan-param non-empty-string $value
     * @phpstan-return self
     */
    public static function of(string $value): self
    {
        return new self(email: $value);
    }

    /**
     * @phpstan-param self $other
     * @phpstan-return bool
     */
    public function equals(self $other): bool
    {
        return $this->email === $other->email;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function value(): string
    {
        return $this->email;
    }

    /**
     * @phpstan-param array<mixed> $arguments
     * @phpstan-return class-string<EmailCast>
     */
    public static function castUsing(array $arguments): string
    {
        return EmailCast::class;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function __toString(): string
    {
        return $this->value();
    }
}
