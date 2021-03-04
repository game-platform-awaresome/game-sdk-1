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

    public static function generateWlcSign(string $secret, array $headers, array $query = null, array $body = null)
    {
        // 数组合并
        $arr = $headers;
        if ($query) {
            $arr = array_merge($headers, $query);
        }
        // 排序
        ksort($arr);
        // 转换位字符串
        array_walk($arr, function(&$value, $key) {
            $value = "{$key}{$value}";
        });
        $str = implode('', $arr);
        // 拼接
        if ($body) {
            $body = json_encode($body, JSON_UNESCAPED_SLASHES);
        }
        $str = $secret . $str . $body;
        return hash("sha256", $str);
    }
}