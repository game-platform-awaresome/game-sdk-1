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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('open_id')->nullable(false)->comment('account表的主键，即account.id');
            $table->string('id_number',20)->nullable(false)->unique()->comment('实名身份证号');
            $table->string('name',10)->nullable(false)->comment('身份证名字');
            $table->string('birthday',10)->nullable(false)->comment('身份证生日');
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
