<?php
namespace App\Helper;
class DES
{
    private static $method = 'DES-CBC';
    private static $key = "abcdefghijklmnop";

    public function __construct()
    {
        // 密钥长度不能超过64bit(UTF-8下为8个字符长度),超过64bit不会影响程序运行,但有效使用的部分只有64bit,多余部分无效,可通过openssl_error_string()查看错误提示
        //self::$key= $key;
    }

    public static function encrypt($plaintext)
    {
        // 生成加密所需的初始化向量, 加密时缺失iv会抛出一个警告
        $ivlen = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivlen);

        // 按64bit一组填充明文
        $plaintext = self::padding($plaintext);
        // 加密数据
        $ciphertext = openssl_encrypt($plaintext, self::$method, self::$key, 1, $iv);
        // 生成hash
        $hash = hash_hmac('sha256', $ciphertext, self::$key, false);

        return base64_encode($iv . $hash . $ciphertext);

    }

    public static function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        // 从密文中获取iv
        $ivlen = openssl_cipher_iv_length(self::$method);
        $iv = substr($ciphertext, 0, $ivlen);
        // 从密文中获取hash
        $hash = substr($ciphertext, $ivlen, 64);
        // 获取原始密文
        $ciphertext = substr($ciphertext, $ivlen + 64);
        // hash校验
        if(hash_equals($hash, hash_hmac('sha256', $ciphertext, self::$key, false)))
        {
            // 解密数据
            $ciphertext = openssl_decrypt($ciphertext, self::$method, '12345678', 1, $iv) ?? false;
            // 去除填充数据
            $plaintext = $ciphertext ? self::unpadding($ciphertext) : false;

            return $plaintext;
        }

        return '解密失败';
    }

    // 按64bit一组填充数据
    private static function padding($plaintext)
    {
        $padding = 8 - (strlen($plaintext)%8);
        $chr = chr($padding);

        return $plaintext . str_repeat($chr, $padding);
    }

    private static function unpadding($ciphertext)
    {
        $chr = substr($ciphertext, -1);
        $padding = ord($chr);

        if($padding > strlen($ciphertext))
        {
            return false;
        }
        if(strspn($ciphertext, $chr, -1 * $padding, $padding) !== $padding)
        {
            return false;
        }

        return substr($ciphertext, 0, -1 * $padding);
    }
}

class DES3 {
	private static $key_s = "my.oschina.net/penngo?#@";
	private static $key_c = "my.oschina.net/penngo?#@";
	private static $iv = "01234567";

	public static function encrypt($input) {
		if (is_array($input)) {
			$input = json_encode($input);
		}
		
		// $size = mcrypt_get_block_size(MCRYPT_3DES,MCRYPT_MODE_CBC);
		// $input = self::pkcs5_pad($input, $size);
		
		// $key_s = str_pad(self::$key_s,24,'0');
		// $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
		// if( self::$iv == '' )
		// {
			// $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		// }
		// else
		// {
			// $iv = self::$iv;
		// }
		// @mcrypt_generic_init($td, $key_s, $iv);
		// $data = mcrypt_generic($td, $input);
		// mcrypt_generic_deinit($td);
		// mcrypt_module_close($td);
		
		$input = self::pkcs5_pad($input, 8);
		$ivlen = openssl_cipher_iv_length("DES-EDE3-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
		$data = openssl_encrypt($input, "DES-EDE3-CBC", self::$key_s, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, self::$iv);

		$data = base64_encode($data);
		return $data;
	}
	public static function decrypt($encrypted) {
		if (gettype($encrypted) != 'string' or strlen($encrypted) < 12) {
			return false;
		}
		$encrypted = base64_decode($encrypted);
		
		// $key_c = str_pad(self::$key_c,24,'0');
		// $td = mcrypt_module_open(MCRYPT_3DES,'',MCRYPT_MODE_CBC,'');
		// if( self::$iv == '' )
		// {
			// $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		// }
		// else
		// {
			// $iv = self::$iv;
		// }
		// $ks = mcrypt_enc_get_key_size($td);
		// @mcrypt_generic_init($td, $key_c, $iv);
		// $decrypted = mdecrypt_generic($td, $encrypted);
		// mcrypt_generic_deinit($td);
		// mcrypt_module_close($td);
		
		$ivlen = openssl_cipher_iv_length("DES-EDE3-CBC");
        $iv = substr($encrypted, 0, $ivlen);
		$decrypted = openssl_decrypt($encrypted, "DES-EDE3-CBC", self::$key_s, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, self::$iv);
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
