<?php
return [
	
	

	// 签名方式
	'sign_type' => 'RSA',
	
	'service' => "alipay.wap.create.direct.pay.by.user",


	//合作身份者id，以2088开头的16位纯数字。
	

	'partner' => '2088811702611944',
	
	//卖家支付宝帐户。
	

	'seller' => '13728088888@139.com',

	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'muu3tn5tklpeuel65q76do6pcwyvsdop',

	// 商户私钥。
	'private_key' => 'MIICXAIBAAKBgQCvMMbgqW7pCiFDG4gKR44d3qRKmSYlxrGSKtyN0A3B658JTis8RUt8nVhfU4lvwAu1UL9d5rpSrmfPf3Up9k6ibQuZPDHa9eqZh13ll70f/Dx6rvePLdB2c9sLKQqbCqtXLOZ75CYHALHLU+HAmr8QM867rx9F6NMiZ8J6TF1haQIDAQABAoGAS4GEVdPwv3PkPh4hlfydHfaVbKlxLZrjcZITmPNw2oGI++O68rETBdRzADLq60UkRrNUp04IRBZzG5VdlAZagSRNyZq1CFFpzl57P2HVJR9aJ7nF5h2i5qqDLw4LA+0XyLTjNT2ZSacHy4V/Op+2caLYHUhINQyIId4JRWNvbFkCQQDZXEhiGw+5MA3GqaokZd38Lyd4uTNoO+SllCMFKKG2RENPDLRWw19KrLYB6FpIJTt5seVkK5hvyMd7O3EFKukbAkEAzlVoz4P6eII1+cmosF4saDaW16UeTyal4IgckaNGWRmbX7pBpRJl33GemSsMBra/ol6GtspyLKm4Kjg6RH0rywJANtjDZwX/FLKcd0muphqSRiU754mMADxEuMdFgvK6w3w8I8FH7DDGZdho4NhZl6TVeiK6iGk7wNFADMd2AGDQiwJAZ8Q+XLZz/a0GdiO7qU8DKDnj7HmG36mBHIV3UKr7Uw30vNTP9LNm9lOOvlsOxkWb5I2DVGRFvT5xdxImA5GrtwJBAKDGwLfPoUBVJHKwP5WSkYwpCEppUSFJ8WH097SxAPwZ5y86sxWY71Un9EE3CvsBNloXa/FRWQ0G6XD+oovuPkE=',

	// 阿里公钥。
	'alipay_public_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB',

	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'j3f0mxqo6qwohue4vtazve7w78rldan9',

	/*//合作身份者id，以2088开头的16位纯数字。
	

	'partner' => '2088421297739730',
	
	//卖家支付宝帐户。
	

	'seller' => 'fb618@qq.com',

	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'muu3tn5tklpeuel65q76do6pcwyvsdop',

	// 商户私钥。
	'private_key' => 'MIICXQIBAAKBgQDn+HTnGDf0E709KiJ8WzOh/vrQ7DkkmG6j1FQVj+2/m/zSG3alSBzt9hMJMIrCbkVlnn+MzflEX2wH0fPPkOPJpz3JDsp7wRKhhMGQD/lsjqtQLst+09dwlSLTXcAY9dtca6PntfTcXAwhY2aV06ZpqXHcSEzIi8Ytjex5G8Ez3wIDAQABAoGADwkzyqbvOYruAUDx602eEyoL5+7n5U9cHJJuwNcLehgKUzQkhKIf/eytXzMeS2wKY4PakK3pMl1dC0S4oQOrQA9XHPpZOWV7zbl3pdNMHlwCNY+wG0Z9EASwcDdR4hQUSDg0Q+YlmSUJonG8TzKmpekxQU5ixBBd4owomvtq9gECQQD2jK2+IqVGmvwzO+yWmJzH+FPdFMF0mhZj3Vi8ovSywkpSUaQ68fL64GjKSq9IlP0PsF/WDMMZFMEeO9bTy6ThAkEA8Ny36Zez2CpQobXkYaM+K+hntxjpVCF63s2ezVdkgWOJ364ZZEMTKgr4CI0j1XFUgTfvNb20Q8+e70JIXccwvwJBAN/EVJhh53GKjuWNOLCB+KHB75OTF/XVOAcRSU2D9OUdU8kc0hqxp58s987KNAaDOp73rDOgx53G6XOgSd1WUeECQAFGeebPudEMME8a4nGt5pde7KWoiRu77aWeWxflPY/90e4QQuwn+sL/Od75uFXZ+sOQY0Dal0jAoavMowt0EYsCQQCl8LpgIvUM/QKsf+OS3zcY9OHfKfqzY3S79AQryLdzVrD6MN+lvTTJYGZUvHlNybCm9bCQ0TqxxYH1i3CmkgJ+',

	// 阿里公钥。
	'alipay_public_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB',
*/
	
	'payment_type' => 1,
	
	// 异步通知连接。
	'notify_url' => 'http://xhplus.feibu.info',

	'return_url' => 'http://xhplus.feibu.info',

	'show_url'	=> 'http://xhplus.feibu.info',
	//字符编码格式 目前支持 gbk 或 utf-8
	'input_charset' => strtolower('utf-8'),

	//ca证书路径地址，用于curl中ssl校验
	//请保证cacert.pem文件在当前文件夹目录中
	'cacert'    => getcwd().'/cacert.pem',

	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	'transport'    => 'http',


];
