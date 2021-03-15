<?php

namespace App\Models;

use App\Tools\CryptTool;
use App\Tools\StringTool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;


/**
 * App\Models\Identity
 *
 * @property int $id
 * @property int $account_id 账号表主键
 * @property string $pi 国家防沉迷体系用户唯一标识
 * @property string $id_number 身份证号
 * @property string $id_name 身份证名字
 * @property string $replace_id_number 隐藏身份证号
 * @property string $replace_id_name 隐藏身份证名字
 * @property string $birthday 身份证生日
 * @property int $status 认证状态
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $age
 * @method static Builder|Identity newModelQuery()
 * @method static Builder|Identity newQuery()
 * @method static Builder|Identity query()
 * @method static Builder|Identity whereAccountId($value)
 * @method static Builder|Identity whereBirthday($value)
 * @method static Builder|Identity whereCreatedAt($value)
 * @method static Builder|Identity whereId($value)
 * @method static Builder|Identity whereIdNumber($value)
 * @method static Builder|Identity whereIdName($value)
 * @method static Builder|Identity whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin Builder

 */
class Identity extends Model
{
    protected $table = 'identities';

    protected $guarded = [];

    protected $appends = ['replace_id_number', 'replace_id_name', 'age'];

    public function getIdNumberAttribute($value)
    {
        return CryptTool::decrypt($value);
    }

    public function getReplaceIdNumberAttribute()
    {
        return StringTool::idNumberReplace($this->id_number);
    }

    public function getIdNameAttribute($value)
    {
        return CryptTool::decrypt($value);
    }

    public function getReplaceIdNameAttribute()
    {
        return StringTool::idNameReplace($this->id_name);
    }

    public function getAgeAttribute()
    {
        $now = Carbon::now();
        $birthday = Carbon::parse($this->birthday);
        return $now->diffInYears($birthday);
    }
}
