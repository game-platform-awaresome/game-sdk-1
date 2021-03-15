## SDK后端 - Laravel 6.x LTS

自买量SDK  
已接入国家网络游戏防沉迷系统中实名认证接口和实名认证结果查询接口

### 环境

- PHP ^7.2.5
    - 扩展 phpredis, opcache, swoole
- Redis ^5.0
- Mysql ^5.7
- Composer ^1.x

### 部署

1. `git clone http://172.16.1.100:9100/platform/xlcw-sdk.git`
2. 线上`composer install --no-dev` 本地`composer install`
3. 在`composer install`过程中会要求解除禁用函数，根据提示解除即可   
4. `cp .env.examle .env`
5. 创建数据库，数据库名：sdk
6. 填写数据库连接参数
7. 填写Redis连接参数
8. 填写APP_URL即IP+PORT，如果有负载均衡和域名，就使用域名
9. 执行数据库迁移
    `php artisan migrate --seed`
10. 正式环境执行，优化执行效率
    - php artisan config:cache 如果遇到配置修改，需要重新缓存
    - php artisan route:cache （闭包函数无法执行）
    - composer dumpautoload -o

### 开发细节记录
1. 问：实名认证接口为什么要设定`如果实名认证24小时内失败三次禁止重试`？  
   答：防沉迷系统虽然没有对失败进行限制，但需要统计认证成功率，要求企业自行控制。
2. 问：为什么要将用户行为数据上报写到`wlc-collect-fatigue`项目？  
   答：主要是因为，用户行为数据上报部分SDK（包括自买量）和游戏都要用到，因此决定单独成项，且golang更适合引入队列来处理。

### 代码架构

```shell
├── app
│   ├── Console // 命令行
│   ├── Constants // 常量
│   ├── Contracts // 接口定义
│   ├── Exceptions // 异常处理
│   ├── Http
│   │   ├── Controllers // 控制层
│   │   ├── Kernel.php // 控制中间件载入、分类
│   │   ├── Middleware // 中间件
│   │   └── Requests // 数据校验
│   ├── Models // 模型层
│   ├── Providers // 服务提供商
│   ├── Repositories // 数据仓库
│   ├── Rules // 校验规则
│   ├── Services // 服务层
│   ├── Traits // 特性
│   └──helpers.php
├── artisan
├── bootstrap
├── composer.json
├── composer.lock
├── config
├── database // 数据库迁移文件及部分初始数据
├── phpunit.xml
├── public // 入口文件
├── readme.md
├── resources // 静态文件和翻译文件
├── routes // 路由
├── server.php
├── storage // 缓存
└── vendor // 第三包composer管理
```

#### 扩展

- godruoyi/php-snowflake 雪花算法，生成唯一open_id
- guzzlehttp/guzzle 发起http请求
- overtrue/easy-sms 短信发送网关（本项目管道执行ucloud和qcloud）
- tymon/jwt-auth jwt生成和加密

#### 日志通道 

- sdk 客户端（SDK）交互日志
- client 客户端错误上传通道
- third 第三方实名认证服务、短信服务日志
- cp 游戏提供商交互日志（token校验、订单查询、订单回调至第三方服务器）
    - query cp方往sdk方发送的二次校验请求和订单查询请求
    - notify sdk方支付信息回调至cp方
- pay 微信、支付宝交互（下单和回调）
    - unifiedOrder 预支付下单
    - notify 微信支付宝回调

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
