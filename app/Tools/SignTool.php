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

    public static function generateWlcSign(array $headers, array $body, string $secret = null)
    {
        // 获取密钥
        $secret = $secret ?? config('services.wlc.app_secret');
        // 数组合并
        $arr = $headers;
        // 排序
        ksort($arr);
        // 转换位字符串
        array_walk($arr, function(&$value, $key) {
            $value = "{$key}{$value}";
        });
        $str = implode('', $arr);
        // 拼接
        $body = json_encode($body, JSON_UNESCAPED_SLASHES);
        $str = $secret . $str . $body;
        return hash("sha256", $str);
    }
}