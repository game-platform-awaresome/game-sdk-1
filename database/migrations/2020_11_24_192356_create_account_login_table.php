<?php

use App\Repositories\AccountLoginRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAccountLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AccountLoginRepository::migrate('account_login_' . getCurrentYear());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_login_' . getCurrentYear());
    }
}
