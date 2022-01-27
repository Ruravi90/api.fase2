<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableSalesCuteUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales',function(Blueprint $table){
            $table->integer('cute_user_id')->nullable()->unsigned();
            $table->foreign('cute_user_id')->references('id')->on('users');
            $table->boolean('is_cute')->default(false);
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales',function(Blueprint $table){
            $table->dropForeign(['cute_user_id']);
            $table->dropColumn(['cute_user_id','is_cute']);
       });
    }
}
