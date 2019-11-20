<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('relationship');
            $table->integer('user_login');
            $table->string('name');
            $table->integer('gender')->default(0);
            $table->integer('birthday');
            $table->integer('code_add');
            $table->bigInteger('start_date');
            $table->bigInteger('end_date');
            $table->integer('start_time');
            $table->integer('end_time');
            $table->integer('address')->comment('1:bệnh viện, 2: nhà, 3:khác')->default(3);
            $table->text('note');
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
        Schema::dropIfExists('patients');
    }
}
