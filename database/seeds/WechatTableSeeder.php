<?php


use App\Models\Wechat;
use Illuminate\Database\Seeder;

class WechatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Wechat::query()->create([
            'app_id' => 1,
            'open_id' => 'wxb0df8bc9f5e153c4',
            'mch_id' => '1539096941',
            'app_secret' => 'A99391ACE45E164750A2443C2A4676A8',
        ]);

        Wechat::query()->create([
            'app_id' => 3,
            'open_id' => 'wx25e3308d8d0a7abb',
            'mch_id' => '1539096941',
            'app_secret' => 'A99391ACE45E164750A2443C2A4676A8',
        ]);
    }
}