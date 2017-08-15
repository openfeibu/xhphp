<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;

class AlipayServiceProvider extends ServiceProvider
{
	/**
	 * boot process
	 */
	public function boot()
	{
		#
	}


	public function register()
	{

		$this->app->bind('alipay.mobile', function ($app)
		{
			$alipay_config = array_merge($app->config->get('alipay-mobile'),$app->config->get('alipay'));
			$alipay = new \App\services\AlipayMobileService($alipay_config);

			//$alipay->setPartner($app->config->get('alipay.partner_id'))
			//	->setSellerId($app->config->get('alipay.seller_id'))
			//	->setSignType($app->config->get('alipay-mobile.sign_type'))
			//	->setPrivateKey($app->config->get('alipay-mobile.private_key'))
			//	->setPublicKey($app->config->get('alipay-mobile.public_key'))
			//	->setNotifyUrl($app->config->get('alipay-mobile.notify_url'));

			return $alipay;
		});

		$this->app->bind('alipay.web', function ($app)
		{
			$alipay = new \App\services\AlipayWebService;

			$alipay->setPartner($app->config->get('alipay.partner'))
				->setSellerId($app->config->get('alipay.seller'))
				->setKey($app->config->get('alipay.key'))
				->setSignType($app->config->get('alipay-web.sign_type'))
				->setNotifyUrl($app->config->get('alipay-web.notify_url'))
				->setReturnUrl($app->config->get('alipay-web.return_url'))
				->setExterInvokeIp($app->request->getClientIp());

			return $alipay;
		});

		$this->app->bind('alipay.wap', function ($app)
		{
			$alipay_config = array_merge($app->config->get('alipay-wap'),$app->config->get('alipay'));
			$alipay = new \App\services\AlipayWapService($alipay_config);

			return $alipay;
		});
		$this->app->bind('alipay-telecom.wap', function ($app)
		{
			$alipay_config =$app->config->get('alipay-telecom-wap');
			$alipay = new \App\services\AlipayWapService($alipay_config);

			return $alipay;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'alipay.mobile',
			'alipay.web',
			'alipay.wap',
		];
	}
}
