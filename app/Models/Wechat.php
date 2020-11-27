<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Wechat
 *
 * @property int $id
 * @property int $app_id 应用id
 * @property string $open_id 微信的app_id
 * @property string $mch_id
 * @property string $app_secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Wechat newModelQuery()
 * @method static Builder|Wechat newQuery()
 * @method static Builder|Wechat query()
 * @mixin \Eloquent
 * @mixin Builder
 */
class Wechat extends Model
{
    protected $table = 'wechat';

    protected $guarded = [];
}