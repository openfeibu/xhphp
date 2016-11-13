<?php

namespace App\Exceptions\Custom;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* 用于用户尚未登陆
*/
class UserUnauthorizedException extends HttpException
{

	public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, array(), $code);
    }
}
