<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiException extends Exception
{
    public function __construct($errors=null, $message=null, $status=500)
    {
        throw new HttpResponseException(new JsonResponse([
            'message' => $message,
            'errors' => $errors,
        ], $status));
    }
}
