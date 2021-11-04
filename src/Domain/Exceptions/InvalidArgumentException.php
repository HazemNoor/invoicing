<?php

namespace Invoicing\Domain\Exceptions;

use LogicException;

class InvalidArgumentException extends LogicException
{
    public static function create(string $argumentName, $argumentValue, string $reason = ''): self
    {
        $argumentValue = is_string($argumentValue) ? "\"$argumentValue\"" : $argumentValue;

        $message = rtrim(
            sprintf(
                "Invalid value [%s::\$%s = %s] %s",
                debug_backtrace()[1]['class'], // Calling Class
                $argumentName,
                $argumentValue,
                $reason
            ),
            ' '
        );

        return new self($message);
    }
}
