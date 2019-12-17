<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexTablePatient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('code_add');
            DB::statement('ALTER TABLE patients ADD FULLTEXT `name` (`name`)'); //đánh index cho cột code_add
            DB::statement('ALTER TABLE patients ENGINE = MyISAM'); // đánh index theo kiểu MyISam ngoài ra còn có kiểu InnoDB nếu không có dòng này cũng được mysql sẽ mặc định là index kiểu MyISAM nhé
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
