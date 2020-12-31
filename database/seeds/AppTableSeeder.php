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
            'secret' => 'b95f8caa39fba1cbc550160e5838c0c4',
            'notify_url' => 'http://106.75.230.85:12001/Notify/pay/channel/l1021',
        ]);

        App::query()->create([
            'id' => 2,
            'name' => '战争工厂',
            'secret' => '9a752a9b513b1a044e1d65d7a1d31209',
            'notify_url' => 'http://106.75.230.85:12001/Notify/pay/channel/l1021',
        ]);

        App::query()->create([
            'id' => 3,
            'name' => '九州幻想',
            'secret' => 'dfdae0de0cbf5424406e259e343a9477',
            'notify_url' => 'http://b2sdk.cmkjgame.com:13006/Notify/Pay/sdk/998',
        ]);

        App::query()->create([
            'id' => 4,
            'name' => '战灵觉醒',
            'secret' => '46b3284f23c07c2b6667d0465fa9c4ab',
            'notify_url' => 'http://b2sdk.cmkjgame.com:13006/Notify/Pay/sdk/998',
        ]);
    }
}
