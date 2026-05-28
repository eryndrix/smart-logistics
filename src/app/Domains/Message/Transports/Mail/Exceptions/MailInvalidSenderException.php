<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail\Exceptions;

/**
 * @phpstan-template TMessage of string
 * @phpstan-template TCode of int
 * @phpstan-template TPrevious of \Throwable|null
 */
final class MailInvalidSenderException extends \RuntimeException
{
    /**
     * @phpstan-param TMessage $message
     * @phpstan-param TCode $code
     * @phpstan-param TPrevious $previous
     */
    public function __construct(
        string $message = 'Invalid sender email.',
        int $code = 550,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: $code,
            previous: $previous
        );
    }
}
