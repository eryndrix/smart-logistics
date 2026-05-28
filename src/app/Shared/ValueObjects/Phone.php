<?php declare(strict_types=1);

namespace App\Shared\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use App\Shared\Casts\PhoneCast;

final class Phone implements Castable
{
    /**
     * @phpstan-var non-empty-string
     */
    private string $phone;

    /**
     * @phpstan-param non-empty-string $phone
     */
    public function __construct(string $phone)
    {
        $normalizedPhone = $this->normalize(phone: $phone);
        $this->ensureValidPhone(phone: $normalizedPhone);
        $this->phone = $normalizedPhone;
    }

    /**
     * @phpstan-param non-empty-string $phone
     * @phpstan-return non-empty-string
     * 
     * @throws \InvalidArgumentException
     */
    private function normalize(string $phone): string
    {
        $phone = trim(string: $phone);
        $phone = preg_replace(
            pattern: '/[^\d+]/',
            replacement: '',
            subject: $phone
        );

        if (!is_string(value: $phone) || $phone === '') {
            throw new \InvalidArgumentException(
                message: 'Phone cannot be empty after normalization.'
            );
        }

        return $phone;
    }

    /**
     * @phpstan-param non-empty-string $phone
     * @phpstan-return void
     * 
     * @throws \InvalidArgumentException
     */
    private function ensureValidPhone(string $phone): void
    {
        if (!(bool) preg_match(
            pattern: '/^\+[1-9]\d{1,14}$/',
            subject: $phone
        )) {
            throw new \InvalidArgumentException(
                message: sprintf(
                    'Invalid phone format: "%s". Expected E.164 format.',
                    $phone
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
        return new self(phone: $value);
    }

    /**
     * @phpstan-param self $other
     * @phpstan-return bool
     */
    public function equals(self $other): bool
    {
        return $this->phone === $other->phone;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function value(): string
    {
        return $this->phone;
    }

    /**
     * @phpstan-param array<mixed> $arguments
     * @phpstan-return class-string<PhoneCast>
     */
    public static function castUsing(array $arguments): string
    {
        return PhoneCast::class;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function __toString(): string
    {
        return $this->value();
    }
}
