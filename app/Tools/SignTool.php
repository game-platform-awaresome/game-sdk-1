<?php

namespace App\Tools;


class SignTool
{
    /**
     * @param array $data
     * @param string $secret
     * @return array
     */
    public static function generateSignToData(array $data, string $secret)
    {
        // 加密
        ksort($data);
        // 签名
        $data['secret'] = $secret;
        // 字符串
        array_walk($data, function(&$value, $key) {
            $value = "{$key}={$value}";
        });
        $str = implode('&', $data);
        // md5+大写
        $data['sign'] = strtoupper(md5($str));
        unset($data['secret']);

        return $data;
    }
}