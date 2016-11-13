<?php

namespace App\Exceptions\Custom;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* 用于账号或密码错误
*/
class UserPasswordIncorrectException extends HttpException
{

	public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, array(), $code);
    }
}
