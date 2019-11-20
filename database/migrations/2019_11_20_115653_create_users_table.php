<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_name');
            $table->string('pass');
            $table->text('name');
            $table->string('email');
            $table->string('phone')->default('');
            $table->string('address_detail')->default('');
            $table->string('avatar');
            $table->integer('type')->comment('1: điều dưỡng, 2: người đại diện');
            $table->integer('block')->default(0);
            $table->string('code_address')->default('');
            $table->integer('gender')->default(0);
            $table->integer('birthday')->default(0);
            $table->text('fcm_token');
            $table->text('provide_id');
            $table->text('type_account')->comment('0:normal,1:fb,2:gg,3:kakaotalk');
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
        Schema::dropIfExists('users');
    }
}
