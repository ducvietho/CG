<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_to');
            $table->integer('user_from');
            $table->integer('user_patient')->default(0);
            $table->integer('type')->comment('1: người bệnh request điều dưỡng,2: người bệnh cancel request,3: người bệnh accepted,4: điều dưỡng request người bệnh, 5: điều dưỡng cancel request, 6: điều dưỡng accept request');
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
        Schema::dropIfExists('notifications');
    }
}
