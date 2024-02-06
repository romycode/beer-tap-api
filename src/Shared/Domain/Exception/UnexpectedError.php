<?php

namespace App\Shared\Domain\Exception;

class UnexpectedError extends \Exception
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct("unexpected error", 0, $previous);
    }
}
