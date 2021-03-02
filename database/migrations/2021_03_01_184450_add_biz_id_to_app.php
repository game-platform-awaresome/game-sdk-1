<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBizIdToApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app', function (Blueprint $table) {
            $table->string('biz_id', 20)->nullable()->comment('国家防沉迷体系备案识别码')->after('secret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app', function (Blueprint $table) {
            $table->dropColumn('biz_id');
        });
    }
}
