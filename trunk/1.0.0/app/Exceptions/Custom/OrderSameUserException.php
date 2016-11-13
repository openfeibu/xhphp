<?php

namespace App\Exceptions\Custom;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* 用于接单人和发单人不能为同一人
*/
class OrderSameUserException extends HttpException
{

	public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, array(), $code);
    }
}
