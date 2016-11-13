<?php
namespace App\Helper;
class DES3 {
	private static $key_s = "my.oschina.net/penngo?#@";
	private static $key_c = "my.oschina.net/penngo?#@";
	private static $iv = "01234567";

	public static function encrypt($input) {
		if (is_array($input)) {
			$input = json_encode($input);
		}
		$size = mcrypt_get_block_size(MCRYPT_3DES,MCRYPT_MODE_CBC);
		$input = self::pkcs5_pad($input, $size);
		$key_s = str_pad(self::$key_s,24,'0');
		$td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
		if( self::$iv == '' )
		{
			$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		}
		else
		{
			$iv = self::$iv;
		}
		@mcrypt_generic_init($td, $key_s, $iv);
		$data = mcrypt_generic($td, $input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$data = base64_encode($data);
		return $data;
	}
	public static function decrypt($encrypted) {
		if (gettype($encrypted) != 'string' or strlen($encrypted) < 12) {
			return false;
		}
		$encrypted = base64_decode($encrypted);
		$key_c = str_pad(self::$key_c,24,'0');
		$td = mcrypt_module_open(MCRYPT_3DES,'',MCRYPT_MODE_CBC,'');
		if( self::$iv == '' )
		{
			$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		}
		else
		{
			$iv = self::$iv;
		}
		$ks = mcrypt_enc_get_key_size($td);
		@mcrypt_generic_init($td, $key_c, $iv);
		$decrypted = mdecrypt_generic($td, $encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$y = self::pkcs5_unpad($decrypted);
		return $y;
	}
	private static function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}
	private static function pkcs5_unpad($text){
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) {
			return false;
		}
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
			return false;
		}
		return substr($text, 0, -1 * $pad);
	}
	public static function PaddingPKCS7($data) {
		$block_size = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		$padding_char = $block_size - (strlen($data) % $block_size);
		$data .= str_repeat(chr($padding_char),$padding_char);
		return $data;
	}
}

// $a = [1=>'a', 2=>'b'];
// $a = json_encode($a);
// echo (new DES3)->encrypt($a);
// var_dump((new DES3)->decrypt('+oT8U3X5UTA+upuOR2uwCr1zS1yk5xvzeXVV\/BNK5XIOc5baCZ4p2SWcuK2HRS2SZ7SwHjLz+Vk='));
// var_dump((new DES3)->decrypt('+oT8U3X5UTDfK6hmVYtDoXGcQ1+OvSe69H333Yaal3mNKl4N7K0NdrDaEMn5F5AYjE9FaFbfe2I4rrrZHFrMTEOT64ZACxWhitzv8+HzJ7c7jadnmhIHUg4X++A7TNJPhyciNlOzxjy5m1X4ZV+hWYtI0DcSwPWlewJnWnPM53Rx3+Whg1FIqnFORa9fFlek9w1eO3FpVw9yCAx6jAbvnY23XaXhkHatumwSW7g36p96PHojgZFlThdgf3TMObmx8SpYO29JzPGpvhYTI1Jp4VERtQERgBYDLHKqPciL6ei0XDKfZbXUoh3QaZKPQX34'));
// var_dump(strlen('11'));
