<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


/**
 * App\Models\Account
 *
 * @property int $id
 * @property string|null $uuid 用户识别码：客户端上传
 * @property string $open_id 唯一的账户id
 * @property string $name 昵称
 * @property string $phone 电话号码
 * @property string $password 密码
 * @property int $os 系统 1: android 2:ios
 * @property int $user_type 用户类型 1：游客 0：正常用户
 * @property string $device 设备id
 * @property int $app_id 注册游戏来源
 * @property string $ip 注册ip
 * @property string|null $token 用户登陆token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 * @mixin \Eloquent
 * @mixin Builder
 */
class Account extends Authenticatable implements JWTSubject
{
    protected $table = 'accounts';

    protected $guarded = [];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
