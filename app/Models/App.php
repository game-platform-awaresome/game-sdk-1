<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\App
 *
 * @property int $id
 * @property string|null $name app 名称
 * @property string $secret
 * @property string $notify_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|App newModelQuery()
 * @method static Builder|App newQuery()
 * @method static Builder|App query()
 * @method static Builder|App whereCreatedAt($value)
 * @method static Builder|App whereId($value)
 * @method static Builder|App whereName($value)
 * @method static Builder|App whereNotifyUrl($value)
 * @method static Builder|App whereSecret($value)
 * @method static Builder|App whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin Builder
 */
class App extends Model
{
    protected $table = 'app';
}
