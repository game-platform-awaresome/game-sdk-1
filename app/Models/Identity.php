<?php

namespace App\Models;

use App\Tools\StringTool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\Identity
 *
 * @property int $id
 * @property int $open_id account表的主键
 * @property string $id_number 实名身份证号
 * @property string $id_name 身份证名字
 * @property string $birthday 身份证生日
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $age
 * @method static Builder|Identity newModelQuery()
 * @method static Builder|Identity newQuery()
 * @method static Builder|Identity query()
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
        return substr_replace($value,'***********',3,11);
    }

    public function getIdNameAttribute($value)
    {
        return StringTool::idNamesReplace($value);
    }

    public function getAgeAttribute()
    {
        $now = Carbon::now();
        $birthday = Carbon::parse($this->birthday);
        return $now->diffInYears($birthday);
    }
}
