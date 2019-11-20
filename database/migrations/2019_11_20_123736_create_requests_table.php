<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_nurse')->comment('user_id điều dưỡng');
            $table->integer('user_patient')->comment('id người bệnh');
            $table->integer('user_login')->comment('user_id người đại diện');
            $table->integer('type')->comment('1: điều dưỡng yêu cầu, 2: người bệnh yêu cầu');
            $table->integer('status')->comment('0:cancel,1:accepted, 2: requesting');
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
        Schema::dropIfExists('requests');
    }
}
