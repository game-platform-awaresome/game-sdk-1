<?php

use App\Models\AppInfo;
use Illuminate\Database\Seeder;

class AppInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppInfo::query()->create([
            'app_id' => 1,
            'app_name' => '那一剑江湖',
            'app_secret' => 'jkh123kjh1k2jh3',
            'notify_url' => 'http://106.75.230.85:12001/Notify/pay/channel/l1021',
        ]);

        AppInfo::query()->create([
            'app_id' => 3,
            'app_name' => '幻剑九歌',
            'app_secret' => 'iUrD9EFhDm5Z6AFF',
            'notify_url' => 'http://b2sdk.cmkjgame.com:13006/Notify/Pay/sdk/999',
        ]);
    }
}
