<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->nullable()->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->float('price', 8, 2);
            $table->float('amount', 8, 2);
            $table->float('balance', 8, 2);
            $table->boolean('is_paid')->nullable();
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
         Schema::table('sales',function(Blueprint $table){
             $table->dropForeign(['client_id']);
             $table->dropForeign(['user_id']);
        }); 

        Schema::dropIfExists('sales');
    }
}
