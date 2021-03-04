<?php


namespace App\Constants;


class IdentityStatus
{
    const SUCCESS = 0;             // 认证成功
    const AUTHING = 1;             // 认证中
    const NO_AUTH = 2;             // 认证失败->未认证
}