<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePatientsTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('patients', function ($table) {
            $table->integer('start_time')->default(0)->change();
            $table->integer('end_time')->default(0)->change();
            $table->integer('start_time_1')->default(0);
            $table->integer('end_time_1')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
