<?php

namespace App\Exceptions\Custom;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* 用于手机号码已注册
*/
class PhoneNumRegisteredException extends HttpException
{

	public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, array(), $code);
    }
}
