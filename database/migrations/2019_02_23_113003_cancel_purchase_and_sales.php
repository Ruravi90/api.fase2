<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CancelPurchaseAndSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('table')->nullable();
            $table->integer('table_id')->nullable();
            $table->string('description');
            $table->timestamps();
        });

        Schema::table('sales',function(Blueprint $table){
            $table->boolean('is_cancel')->default(false);
        });

        Schema::table('purchases',function(Blueprint $table){
            $table->boolean('is_cancel')->default(false);
        });

/*
        Schema::table('logs',function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales',function(Blueprint $table){
            $table->dropColumn(['is_cancel']);
        });

        Schema::table('purchases',function(Blueprint $table){
            $table->dropColumn(['is_cancel']);
        });

        
        Schema::dropIfExists('logs');
    }
}
