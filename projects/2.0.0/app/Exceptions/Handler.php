<?php

namespace App\Exceptions;

use Log;
use Exception;
use App\Helper\DES3;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }
        $response = $this->handle($e, $request);
        if ($response) {
            return $response;
        }
        return parent::render($request, $e);
    }

    /**
     * handle custom exception
     */
    public function handle($e, $request)
    {
        switch ($e) {
            case ($e instanceof \App\Exceptions\Custom\OutputServerMessageException):
                $resposeJson = [
                    'code' => 110,
                    'detail' => sprintf(config('error.110'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\RequestSuccessException):
                $resposeJson = [
                    'code' => 200,
                    'detail' => sprintf(config('error.200'), $e->getMessage() ?: '请求成功'),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\RequestFailedException):
                $resposeJson = [
                    'code' => 400,
                    'detail' => sprintf(config('error.400'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\AccessForbiddenException):
                $resposeJson = [
                    'code' => 403,
                    'detail' => sprintf(config('error.403'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\ParameterMissingOrIllegalException):
                $resposeJson = [
                    'code' => 1001,
                    'detail' => sprintf(config('error.1001'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\RequestTooFrequentException):
                $resposeJson = [
                    'code' => 1002,
                    'detail' => sprintf(config('error.1002'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\FoundNothingException):
                $resposeJson = [
                    'code' => 1003,
                    'detail' => sprintf(config('error.1003'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\UserUnauthorizedException):
                $resposeJson = [
                    'code' => 2001,
                    'detail' => sprintf(config('error.2001'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\PhoneNumRegisteredException):
                $resposeJson = [
                    'code' => 2002,
                    'detail' => sprintf(config('error.2002'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\PhoneNumUnregisteredException):
                $resposeJson = [
                    'code' => 2003,
                    'detail' => sprintf(config('error.2003'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\CaptchaSMSIncorrectException):
                $resposeJson = [
                    'code' => 2004,
                    'detail' => sprintf(config('error.2004'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\CaptchaImageIncorrectException):
                $resposeJson = [
                    'code' => 2005,
                    'detail' => sprintf(config('error.2005'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\UserPasswordIncorrectException):
                $resposeJson = [
                    'code' => 2006,
                    'detail' => sprintf(config('error.2006'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\UserBanningException):
                $resposeJson = [
                    'code' => 2007,
                    'detail' => sprintf(config('error.2007'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\UserPermissionDeniedException):
                $resposeJson = [
                    'code' => 2008,
                    'detail' => sprintf(config('error.2008'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\VersionTooHighException):
                $resposeJson = [
                    'code' => 2009,
                    'detail' => sprintf(config('error.2009'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\VersionTooLowException):
                $resposeJson = [
                    'code' => 2010,
                    'detail' => sprintf(config('error.2010'), $e->getMessage() ?: ''),
                ];
                break;

            case ($e instanceof \App\Exceptions\Custom\OrderSameUserException):
                $resposeJson = [
                    'code' => 2011,
                    'detail' => sprintf(config('error.2011'), $e->getMessage() ?: ''),
                ];
                break;
            case ($e instanceof \App\Exceptions\Custom\UserNotAssociationChiefException):
	            $resposeJson = [
	                'code' => 2012,
	                'detail' => sprintf(config('error.2012'), $e->getMessage() ?: ''),
	            ];
            break;

            default:
                return false;
                break;
        }

       	/*if ($request->isDecrypt === 1) {
            $resposeJson = ['data' => DES3::encrypt($resposeJson)];
        }*/
        return $resposeJson;
        Log::debug('reponse----------'.response()->json($resposeJson));

        return response()->json($resposeJson, 200, ['Access-Control-Allow-Origin' => config('app.allow_origin'),
                                                    'Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie, Accept',
                                                    'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, OPTIONS',
                                                    'Access-Control-Allow-Credentials' => 'true']);


    }
}
