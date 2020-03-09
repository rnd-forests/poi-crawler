<?php

namespace App\Exceptions;

class InternalException extends ApiException
{
    /**
     * BadRequestException constructor.
     * @param string $message
     */
    public function __construct($message='Server error')
    {
        parent::__construct(null, $message, 500);
    }
}
