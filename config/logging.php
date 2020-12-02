<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        // 通用error日志
        'error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/error/error.log'),
            'level' => 'info',
            'days' => 30,
        ],

        // API SDK交互
        'sdk' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sdk/sdk.log'),
            'level' => 'info',
            'days' => 30,
        ],

        // CP服务 token校验、订单状态查询
        'cp_query' => [
            'driver' => 'daily',
            'path' => storage_path('logs/cp/query.log'),
            'level' => 'info',
            'days' => 30,
        ],

        // 回调CP
        'cp_notify' => [
            'driver' => 'daily',
            'path' => storage_path('logs/cp/notify.log'),
            'level' => 'info',
            'days' => 30,
        ],

        // 微信、支付宝交互（下单和回调）
        'pay' => [
            // 日志驱动模式
            'driver' => 'daily',
            'path'   => storage_path('logs/pay/notify.log'),
            'level'  => 'info',
            'days'   => 30
        ],

        // 客户端日志上报日志通道
        'client' => [
            'driver' => 'daily',
            'path' => storage_path('logs/client/client.log'),
            'lever' => 'error',
            'days' => 30
        ],

        // 第三方服务日志上报日志通道
        'third' => [
            'driver' => 'daily',
            'path' => storage_path('logs/third/third.log'),
            'lever' => 'error',
            'days' => 30
        ],
    ],

];
