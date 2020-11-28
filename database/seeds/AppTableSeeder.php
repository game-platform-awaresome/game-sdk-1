<?php

use App\Models\App;
use Illuminate\Database\Seeder;

class AppTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App::query()->create([
            'id' => 1,
            'name' => '那一剑江湖',
            'secret' => 'jkh123kjh1k2jh3',
            'notify_url' => 'http://106.75.230.85:12001/Notify/pay/channel/l1021',
        ]);

        App::query()->create([
            'id' => 2,
            'name' => '战争工厂',
            'secret' => 'abcd123asdasdzxc',
            'notify_url' => 'http://106.75.230.85:12001/Notify/pay/channel/l1021',
        ]);

        App::query()->create([
            'id' => 3,
            'name' => '幻剑九歌',
            'secret' => 'iUrD9EFhDm5Z6AFF',
            'notify_url' => 'http://b2sdk.cmkjgame.com:13006/Notify/Pay/sdk/999',
        ]);
    }
}
