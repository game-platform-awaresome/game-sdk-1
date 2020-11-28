<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index()->comment('账号表主键');
            $table->string('id_number',64)->unique()->comment('加密后身份证号');
            $table->string('id_name',64)->comment('加密后身份证名字');
            $table->date('birthday')->comment('身份证生日');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identities');
    }
}
