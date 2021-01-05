<?php


namespace App\Tools;

use Godruoyi\Snowflake\Snowflake;

class StringTool
{
    /**
     * 返回随机数字+字母组合，需指定长度
     *
     * @param int $length 需要返回的随机字符串长度
     * @return string 返回的随机字符串
     */
    public static function randomKey(int $length)
    {
        $charPool = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $charPool{mt_rand(0, 35)};
        }
        return $key;
    }
    
    /**
     * 雪花算法
     * 生成open_id
     *
     * @return string
     */
    public static function generateOpenId()
    {
        $snowflake = new Snowflake();
        return $snowflake->id();
    }

    /**
     * @param string $idNumber
     * @return string
     */
    public static function idNumberReplace(string $idNumber)
    {
        return substr_replace($idNumber,'***********',3,11);
    }

    /**
     * @param string $idName
     * @return string
     */
    public static function idNameReplace(string $idName)
    {
        $strLen = mb_strlen($idName, 'utf-8');
        $firstStr = mb_substr($idName, 0, 1, 'utf-8');
        $lastStr = mb_substr($idName, -1, 1, 'utf-8');

        return $strLen == 2 ? $firstStr . str_repeat('*', mb_strlen($idName, 'utf-8') - 1) : $firstStr . str_repeat("*", $strLen - 2) . $lastStr;
    }
}