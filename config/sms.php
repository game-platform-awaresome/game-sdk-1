<?php

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'ucloud',
            'qcloud',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => storage_path('/logs/third/sms.log'),
        ],
        'qcloud' => [
            'sdk_app_id' => '1400208860',
            'app_key' => '47a32768465f9512eab663ee8e5130e7',
            'sign_name' => ''
        ],
        'ucloud' => [
            'private_key'  => '0UoJv8IY2Y8kEMAUjsNAZEFquIlUWLaTF76uaLYeA9AUDsYbgR65fCn0o+wFsrrI',
            'public_key'   => 'UVy7/744vPPUDDwLhcp4OqALUu99+Y78Y9pCxKLQNSehaffHCypZazmPe7k=',
            'sig_content'  => 'SIG202011267B7A72',
            'project_id'   => 'org-lcsntm'
        ],
    ],
];