<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\AppInfo
 *
 * @property int $id
 * @property int $app_id
 * @property string|null $app_name app 名称
 * @property string $app_secret
 * @property string $notify_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|AppInfo newModelQuery()
 * @method static Builder|AppInfo newQuery()
 * @method static Builder|AppInfo query()
 * @mixin \Eloquent
 * @mixin Builder
 */
class AppInfo extends Model
{
    protected $table = 'app_info';
}
