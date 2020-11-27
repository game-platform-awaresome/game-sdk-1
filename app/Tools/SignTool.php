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
        $data['sign'] = strtoupper(md5(http_build_query($data)));
        unset($data['secret']);

        return $data;
    }
}