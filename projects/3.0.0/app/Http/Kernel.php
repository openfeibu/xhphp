<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	protected $middleware = [
        	\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
	        \App\Http\Middleware\EncryptCookies::class,
	        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
	        \Illuminate\Session\Middleware\StartSession::class,
	        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    ];
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middlewareGroups = [
    	'web' => [

	        // \App\Http\Middleware\VerifyCsrfToken::class,
	        \App\Http\Middleware\DES3Decrypt::class,
	        \App\Http\Middleware\DES3Encrypt::class,
	        \App\Http\Middleware\EnableCrossRequestMiddleware::class,
	    ],
	    'business' =>[

	    ],
		'telecom' => [

		],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\UserAuthenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'business' => \App\Http\Middleware\BusinessAuthenticate::class,
		'mbusiness' => \App\Http\Middleware\MobileBusinessAuthenticate::class,
		'telecom' => \App\Http\Middleware\TelecomAuthenticate::class,
    ];
}
