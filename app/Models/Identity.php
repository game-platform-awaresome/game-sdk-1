<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;


/**
 * App\Models\Identity
 *
 * @property int $id
 * @property int $account_id 账号表主键
 * @property string $id_number 身份证号
 * @property string $id_name 身份证名字
 * @property string $birthday 身份证生日
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

    protected $appends = ['age'];

    public function getIdNumberAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function getIdNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function getAgeAttribute()
    {
        $now = Carbon::now();
        $birthday = Carbon::parse($this->birthday);
        return $now->diffInYears($birthday);
    }
}
