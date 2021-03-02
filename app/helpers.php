<?php

use Illuminate\Support\Facades\Route;

/**
 * 获取当前控制器与方法
 *
 * @return array
 */
if (!function_exists('getCurrentAction')) {

    function getCurrentAction()
    {
        $action = Route::current()->getActionName();
        list($class, $method) = explode('@', $action);

        return ['controller' => $class, 'method' => $method];
    }
}


/**
 * 获取当前控制器名
 *
 * @return string
 */
if (!function_exists('getCurrentControllerName')) {

    function getCurrentControllerName()
    {
        return getCurrentAction()['controller'];
    }
}

/**
 * 获取当前方法名
 *
 * @return string
 */
if (!function_exists('getCurrentMethodName')) {

    function getCurrentMethodName()
    {
        return getCurrentAction()['method'];
    }
}

/**
 * xml转数组
 *
 * @return array
 */
if (!function_exists('xmlToArray')) {

    function xmlToArray($data)
    {
        if (!$data) {
            return [];
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}

/**
 * xml转数组
 *
 * @return array
 */
if (!function_exists('arrayToXml')) {

    function arrayToXml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            return null;
        }

        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= is_numeric($val) ? '<'.$key.'>'.$val.'</'.$key.'>' :
                '<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
        }
        $xml .= '</xml>';

        return $xml;
    }
}

/**
 * xml转数组
 *
 * @return array
 */
if (!function_exists('getCurrentYear')) {

    function getCurrentYear()
    {
        return date('Y');
    }
}
