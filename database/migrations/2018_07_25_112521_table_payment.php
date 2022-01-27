<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('responsible_id')->unsigned();
            $table->foreign('responsible_id')->references('id')->on('users');
            $table->integer('type_sale_id')->unsigned();
            $table->foreign('type_sale_id')->references('id')->on('cat_type_sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments',function(Blueprint $table){
             $table->dropForeign(['responsible_id','type_sale_id']);
             $table->dropColumn(['responsible_id','type_sale_id']);
        });
    }
}
