<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPiAndStatusToIdentities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('identities', function (Blueprint $table) {
            $table->string('id_number')->nullable()->change();
            $table->string('id_name')->nullable()->change();
            $table->date('birthday')->nullable()->change();
            $table->string('pi', 64)->nullable()->comment('国家防沉迷体系用户唯一标识')->after('account_id');
            $table->unsignedTinyInteger('status')->comment('认证状态，请查看IdentityStatus')->after('birthday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('identities', function (Blueprint $table) {
            $table->dropColumn(['pi', 'status']);
        });
    }
}
