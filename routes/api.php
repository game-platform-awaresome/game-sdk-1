<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 微信、支付宝支付回调
// pay日志中间件
Route::middleware(['log.pay'])->group(function () {
    Route::post('pay/wechatNotify', 'NotifyController@wechat')
        ->name('api.pay.wechat.notify');
    Route::post('pay/alipayNotify', 'NotifyController@alipay')
        ->name('api.pay.alipay.notify');
});
// SDK接口
// api日志中间件、签名验证中间件、频率限制中间件
Route::middleware(['log.sdk', 'check.sdk.param', 'check.sdk.signature', 'throttle:60,1'])->group(function () {
    // Token刷新
    // 比其余SDK接口多一个Token校验中间件
    Route::middleware('auth.token')->post('token/refresh', 'AccountController@tokenRefresh')
        ->name('api.token.refresh');
    // 请求短信验证码发送
    Route::post('getValidationCode', 'SmsController@getValidationCode')
        ->name('api.sms.getValidationCode');
    // 验证校验码
    Route::post('checkValidationCode', 'SmsController@checkValidationCode')
        ->name('api.sms.checkValidationCode');
    // 游客登陆，uuid登录
    Route::post('visitorLogin', 'AccountController@visitorLogin')
        ->name('api.visitor.login');
    // 游客绑定
    Route::post('visitorBind', 'AccountController@visitorBind')
        ->name('api.visitor.bind');
    // 账号注册
    Route::post('accountRegister', 'AccountController@accountRegister')
        ->name('api.account.register');
    // 手机号密码登录
    Route::post('accountLogin', 'AccountController@accountLogin')
        ->name('api.account.login');
    // 找回密码
    Route::post('findPassword', 'AccountController@findPassword')
        ->name('api.account.findPassword');
    // 实名认证绑定接口
    Route::post('bindIdentity', 'AccountController@bindIdentity')
        ->name('api.account.bindIdentity');
    // 实名认证换绑接口
    Route::post('changeIdentity', 'AccountController@changeIdentity')
        ->name('api.account.changeIdentity');
    // 生成订单
    Route::post('orders', 'OrderController@store')
        ->name('api.orders.store');
    // 取消支付
    Route::put('orders', 'OrderController@update')
        ->name('api.orders.update');
    // 预支付
    Route::post('orders/unifiedOrder', 'OrderController@unifiedOrder')
        ->name('api.orders.unifiedOrder');
    // 客户端错误日志上报
    Route::post('client/errors', 'ClientErrorController@report')
        ->name('api.client.errors.report');
});
// 供所接游戏进行登录验证、查询订单号
// Third日志中间件
Route::middleware(['log.cp', 'check.cp.param'])->group(function () {
    // 比其余SDK接口多一个Token校验中间件
    Route::middleware('auth.token')->post('tokenValidation', 'TokenController@validateToken')
        ->name('api.token.validation');
    Route::post('orders/status', 'OrderController@orderStatus')
        ->name('api.orders.status');
});
// 供测试使用，后续进行WEB
Route::get('identity/del', 'AccountController@delIdentity');
Route::get('identity/changeBirthday', 'AccountController@changeIdentityBirthday');
