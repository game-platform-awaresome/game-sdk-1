<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\AccountLogin
 *
 * @property int $id
 * @property string $open_id 唯一的账户id
 * @property string $phone 电话号码
 * @property int $os 系统 1: android 2:ios
 * @property int $user_type 用户类型 1：游客 0：正常用户
 * @property string $device 设备
 * @property string $ip 登录ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|AccountLogin newModelQuery()
 * @method static Builder|AccountLogin newQuery()
 * @method static Builder|AccountLogin query()
 * @mixin \Eloquent
 * @mixin Builder
 */
class AccountLogin extends Model
{
    protected $table = 'account_login_2020';

    protected $guarded = [];
}
