<?php
/**
 * Created by PhpStorm.
 * User: tranghv
 * Date: 31/08/2018
 * Time: 17:51
 */

namespace App\Exceptions;

class UnauthorizedException extends ApiException
{
    /**
     * UnauthorizedException constructor.
     * @param mixed $errors
     * @param string $message
     */
    public function __construct($errors=null, $message='Xác thực thất bại')
    {
        parent::__construct($errors, $message, 401);
    }
}
