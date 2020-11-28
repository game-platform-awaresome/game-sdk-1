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
 * @property string $open_id 账户id
 * @property string|null $uuid 用户识别码：客户端上传；如果是正常用户则不存在uuid
 * @property string $phone 电话号码
 * @property string $name 昵称
 * @property string $password 密码
 * @property int $user_type 用户类型 1：游客 0：正常用户
 * @property int $os 注册系统 1: android 2:ios
 * @property string $device 注册设备
 * @property int $app_id 注册游戏
 * @property string $ip 注册ip
 * @property string|null $token 用户登陆token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 * @method static Builder|Account whereAppId($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereDevice($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereIp($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereOpenId($value)
 * @method static Builder|Account whereOs($value)
 * @method static Builder|Account wherePassword($value)
 * @method static Builder|Account wherePhone($value)
 * @method static Builder|Account whereToken($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @method static Builder|Account whereUserType($value)
 * @method static Builder|Account whereUuid($value)
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
