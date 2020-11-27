<?php

namespace App\Exceptions;

class Code
{
    const UNKNOWN_EXCEPTION = -1;                                   // 未知异常
    const SUCCESS = 0;                                              // 成功
    const FAIL = 1;                                                 // 失败
    const PARAM_ERROR = 1000;                                       // 参数错误
    const UNPACK_EXCEPTION = 1001;                                  //
    const TYPE_CONVERT_ERROR = 1003;                                //
    const SIGNATURE_FAIL = 1004;                                    // 验签不通过
    const TIME_EXPIRED = 1005;                                      // 时间过期
    const TOKEN_NOT_PROVIDED = 1006;                                // Token未提供
    const UNAUTHORIZED = 1007;                                      // 未授权
    const UNKNOWN_TOKEN = 1008;                                     //
    const INVALID_TOKEN = 1009;                                     // Token认证失败
    const PARSE_TOKEN_FAIL = 1010;                                  //
    const UNKNOWN_USER = 1011;                                      //
    const UPDATE_ACCOUNT_FAIL = 1012;                               // 订单更新失败
    const LOGIN_LOG_RECORD_FAIL = 1013;                             // 登录日志记录失败
    const VERIFICATION_FAILED = 1014;                               // 短信验证码不通过
    const SERVER_NO_OPENID = 1015;                                  //
    const WRONG_REQUEST_ATTRIBUTE = 1017;                           // 缺少必要参数（中间件）
    const ACCOUNT_REGISTERED_FAIL = 1018;                           // 账号注册失败
    const ACCOUNT_LOGIN_WRONG_PASSWORD = 1019;                      // 账号密码登录失败（用户不存在、密码错误等）
    const USER_LOGIN_QUERY_EXCEPTION = 1020;                        //
    const VISITOR_LOGIN_QUERY_EXCEPTION = 1021;                     // 游客登录失败
    const VISITOR_LOGIN_WRONG_PASSWORD = 1022;                      //
    const VISITOR_LOGIN_FAIL = 1023;                                // 无效的UUID，登录失败
    const TOKEN_LOGIN_FAIL = 1024;                                  // Token登录失败
    const INVALID_UUID = 1025;                                      // 无效的UUID
    const INVALID_OPEN_ID = 1026;                                   // 无效的OPEN_ID
    const PHONE_NUMBER_UNREGISTERED = 1027;                         // 手机号码未注册
    const REDIS_SET_ERROR = 1028;                                   //
    const REDIS_GET_ERROR = 1029;                                   //
    const REDIS_DELETE_ERROR = 1030;                                //
    const INVALID_APP_ID = 1031;                                    // 无效的APPID
    const BAD_PARAMS = 1032;                                        //
    const HANDLE_SDK_ERROR = 1033;                                  //
    const PHONE_NUMBER_REGISTERED = 1034;                           // 手机号码已注册
    const PHONE_NUMBER_FORMAT_NOT_CORRECT = 1035;                   // 手机号码格式不正确
    const DAILY_FREQUENCY_LIMITED = 1036;                           //
    const PAY_SIGN_ERROR = 1037;                                    //
    const CREATE_ORDER_FAIL = 1037;                                 //
    const INVALID_ORDER_ID = 1038;                                  // 订单查找失败
    const THE_SAME_PASSWORD = 1039;                                 //
    const INSUFFICIENT_PACKAGE_BALANCE = 1040;                      // 套餐余额不足
    const CREATE_WECHAT_PAY_FAIL = 1041;                            // 创建微信统一下单失败
    const CREATE_ALIPAY_PAY_FAIL = 1042;                            // 创建支付宝统一下单失败
    const UPDATE_ORDER_FAIL = 1044;                                 // 更新订单失败
    const ORDER_STATUS_ERROR = 1045;                                // 订单已取消
    const AMOUNT_NOT_MATCH = 1046;                                  // 回调数额不匹配
    const ORDER_CANNOT_REPEAT = 1047;                               // 不能重复下单
    const ORDER_DATABASE_NOT_FOUND = 1048;                          // 订单数据库未创建
    const RECORD_CLIENT_LOG_ERROR = 1049;                           // 记录客户端日志出现错误
    const WRONG_PASSWORD_FORMAT = 1050;                             // 密码格式错误
    const ID_INFO_ALREADY_EXIST = 1051;                             // 身份证信息已存在于数据库
    const ID_INFO_DOES_NOT_MATCH = 1052;                            // 旧身份证信息与数据库不匹配
    const IDENTIFY_FAIL = 1053;                                     // 实名认证失败
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
