<?php

namespace App\Exceptions\Custom;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* 用于直接输出服务器返回的信息
*/
class OutputServerMessageException extends HttpException
{

	public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, array(), $code);
    }
}
