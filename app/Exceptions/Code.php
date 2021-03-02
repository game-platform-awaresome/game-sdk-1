<?php

namespace App\Exceptions;

class Code
{
    const UNKNOWN_EXCEPTION = -1;                                   // 未知异常
    const SUCCESS = 0;                                              // 成功
    // 1 校验层
    const WRONG_REQUEST_ATTRIBUTE = 1001;                           // 缺少必要参数（中间件）
    const BAD_PARAMS = 1002;                                        // 参数校验不过（缺少参数，参数类型错误）
    const TIME_EXPIRED = 1003;                                      // 时间过期
    const SIGNATURE_FAIL = 1004;                                    // 验签不通过
    const TOKEN_NOT_PROVIDED = 1005;                                // Token未提供
    const INVALID_TOKEN = 1006;                                     // Token认证失败
    const TOKEN_NOT_FOUND_ACCOUNT = 1007;                           // Token无法找到账号
    // 1 账户管理、实名管理
    const ACCOUNT_REGISTERED_FAIL = 1010;                           // 账号注册失败
    const PHONE_NUMBER_UNREGISTERED = 1011;                         // 手机号码未注册
    const PHONE_NUMBER_REGISTERED = 1012;                           // 手机号码已注册
    const PHONE_NUMBER_FORMAT_NOT_CORRECT = 1013;                   // 手机号码格式不正确
    const ACCOUNT_LOGIN_WRONG_PASSWORD = 1014;                      // 账号密码登录失败（用户不存在、密码错误等）
    const INVALID_OPEN_ID = 1015;                                   // 无效的OPEN_ID
    const VISITOR_LOGIN_FAIL = 1016;                                // 无效的UUID，登录失败
    const INVALID_UUID = 1017;                                      // 无效的UUID
    const UPDATE_ACCOUNT_FAIL = 1018;                               // 账户更新失败
    const THE_SAME_PASSWORD = 1019;                                 // 新旧密码一致
    const WRONG_PASSWORD_FORMAT = 1020;                             // 密码格式错误
    const ID_INFO_ALREADY_EXIST = 1021;                             // 身份证信息已存在于数据库
    const ID_INFO_DOES_NOT_MATCH = 1022;                            // 旧身份证信息与数据库不匹配
    const IDENTIFY_FAIL = 1023;                                     // 实名认证失败
    const ID_INFO_DOES_NOT_EXIST = 1024;                            // 旧身份证信息未找到
    const IDENTIFY_ING = 1025;                                      // 实名认证中
    // 1 游戏管理
    const INVALID_APP_ID = 1030;                                    // 无效的APPID
    // 1 订单管理
    const CREATE_ORDER_FAIL = 1040;                                 // 创建订单失败
    const INVALID_ORDER_ID = 1041;                                  // 订单查找失败
    const UPDATE_ORDER_FAIL = 1042;                                 // 更新订单失败
    const ORDER_STATUS_ERROR = 1043;                                // 订单状态异常
    const AMOUNT_NOT_MATCH = 1044;                                  // 回调数额不匹配
    const ORDER_DATABASE_NOT_FOUND = 1045;                          // 订单数据库未创建
    // 1 登录日志
    const LOGIN_LOG_RECORD_FAIL = 1050;                             // 登录日志记录失败
    // 2 微信服务异常
    const WECHAT_SERVICE_ERROR = 2000;                              // 微信服务错误
    const WECHAT_VERIFY_SIGN_FAIL = 2001;                           // 微信签名错误
    const WECHAT_INVALID_ARGUMENT = 2002;                           // 微信无效参数
    // 3 支付宝服务异常
    const ALIPAY_SERVICE_ERROR = 3000;                              // 支付宝服务错误
    const ALIPAY_VERIFY_SIGN_FAIL = 3001;                           // 支付宝签名错误
    const ALIPAY_INVALID_CONFIG = 3002;                             // 支付宝无效配置
    // 4 短信服务异常、身份二要素检查服务异常
    const SMS_ERROR = 4001;                                         // 短信发送失败
    const SMS_SENT = 4002;                                          // 短信已发送
    const INVALID_SMS_CODE = 4003;                                  // 短信已发送
    const SMS_CODE_VERIFY_FAIL = 4004;                              // 短信验证码验证错误
}
