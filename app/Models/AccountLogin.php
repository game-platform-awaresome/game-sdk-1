<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\AccountLogin
 *
 * @property int $id
 * @property int $account_id account主键
 * @property int $user_type 登录用户类型 1：游客 0：正常用户
 * @property int $os 登录系统 1: android 2:ios
 * @property string $device 登录设备
 * @property string $ip 登录ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|AccountLogin newModelQuery()
 * @method static Builder|AccountLogin newQuery()
 * @method static Builder|AccountLogin query()
 * @method static Builder|AccountLogin whereAccountId($value)
 * @method static Builder|AccountLogin whereCreatedAt($value)
 * @method static Builder|AccountLogin whereDevice($value)
 * @method static Builder|AccountLogin whereId($value)
 * @method static Builder|AccountLogin whereIp($value)
 * @method static Builder|AccountLogin whereOs($value)
 * @method static Builder|AccountLogin whereUpdatedAt($value)
 * @method static Builder|AccountLogin whereUserType($value)
 * @mixin \Eloquent
 * @mixin Builder
 */
class AccountLogin extends Model
{
    protected $table = 'account_login_2020';

    protected $guarded = [];
}
