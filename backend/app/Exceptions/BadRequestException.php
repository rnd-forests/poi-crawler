<?php

namespace App\Exceptions;

class BadRequestException extends ApiException
{
    /**
     * BadRequestException constructor.
     * @param mixed $errors
     * @param string $message
     */
    public function __construct($errors=null, $message='Bad request')
    {
        parent::__construct($errors, $message, 400);
    }
}
