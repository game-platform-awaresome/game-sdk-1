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
        $dataSign = $data;
        // 排序
        ksort($dataSign);
        // 签名
        $dataSign['secret'] = $secret;
        // 字符串
        array_walk($dataSign, function(&$value, $key) {
            $value = "{$key}={$value}";
        });
        $str = implode('&', $dataSign);
        // md5+大写
        $data['sign'] = strtoupper(md5($str));

        return $data;
    }
}