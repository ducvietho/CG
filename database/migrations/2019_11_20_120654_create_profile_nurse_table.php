<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileNurseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_nurse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nationality')->default('');
            $table->bigInteger('start_date');
            $table->bigInteger('end_date');
            $table->integer('start_time');
            $table->integer('end_time');
            $table->integer('address')->comment('1:bệnh viện, 2: nhà, 3:khác')->default(3);
            $table->integer('is_certificate')->comment('có chứng chỉ hay k')->default(0);
            $table->string('description')->default('');
            $table->float('rate')->default(0.0);
            $table->integer('user_login');
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
        Schema::dropIfExists('profile_nurse');
    }
}
