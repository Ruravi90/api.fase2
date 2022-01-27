<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('lastname');
            $table->string('motherlastname')->nullable();
            $table->string('email')->nullable();
            
            $table->string('phone_home')->nullable();
            $table->string('phone_mobile')->nullable();
            $table->integer('reference_id')->unsigned();
            $table->foreign('reference_id')->references('id')->on('cat_references');
            $table->timestamps();//created_at and update_at
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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['reference_id']);
        }); 

        Schema::dropIfExists('clients');
    }
}
