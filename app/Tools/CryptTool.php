<?php


namespace App\Tools;


class CryptTool
{
    /**
     * 加密
     *
     * @param $string
     * @param string $key
     * @return string|string[]
     */
    public static function encrypt($string, $key = 'crypt')
    {
        if (!$string) {
            return null;
        }

        $result = self::crypt(substr(md5($string . $key), 0, 8) . $string, $key);

        return str_replace('=', '', base64_encode($result));
    }

    /**
     * 解密
     *
     * @param $string
     * @param string $key
     * @return false|string
     */
    public static function decrypt($string, $key = 'crypt')
    {
        $result = self::crypt(base64_decode($string), $key);

        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return '';
        }
    }

    protected static function crypt($string, $key = 'crypt')
    {
        $key = md5($key);
        $key_length = strlen($key);
        $string_length = strlen($string);
        $randKey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $randKey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $randKey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        return $result;
    }

    public static function aes128gcm(array $data, string $secret = null)
    {
        // 获取密钥
        $secret = $secret ?? config('services.wlc.app_secret');
        // 将secret转成ASC码
        $secret = hex2bin($secret);
        // 将data转为字符串
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $cipher = "aes-128-gcm";
        $iv_len = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $encrypt = openssl_encrypt($data, $cipher, $secret, OPENSSL_RAW_DATA, $iv,$tag);
        return base64_encode(($iv.$encrypt.$tag));
    }
}