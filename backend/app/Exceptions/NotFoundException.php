<?php

namespace App\Exceptions;

class NotFoundException extends ApiException
{
    /**
     * BadRequestException constructor.
     * @param mixed $errors
     * @param string $message
     */
    public function __construct($errors=null, $message='Không tìm thấy')
    {
        parent::__construct($errors, $message, 404);
    }
}
