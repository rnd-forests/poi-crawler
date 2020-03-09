<?php

namespace App\Exceptions;

class ValidationException extends ApiException
{
    public function __construct($errors=null, $message='Dữ liệu không hợp lệ')
    {
        parent::__construct($errors, $message, 422);
    }
}
