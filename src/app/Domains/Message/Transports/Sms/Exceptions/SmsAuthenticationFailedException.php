<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms\Exceptions;

/**
 * @phpstan-template TMessage of string
 * @phpstan-template TCode of int
 * @phpstan-template TPrevious of \Throwable|null
 */
final class SmsAuthenticationFailedException extends \RuntimeException
{
    /**
     * @phpstan-param TMessage $message
     * @phpstan-param TCode $code
     * @phpstan-param TPrevious $previous
     */
    public function __construct(
        string $message = 'SMS provider authentication failed.',
        int $code = 20003,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: $code,
            previous: $previous
        );
    }
}
