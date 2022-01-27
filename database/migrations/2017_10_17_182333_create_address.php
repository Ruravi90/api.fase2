<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('address', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('inner_number');
            $table->integer('outdoor_number')->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('town')->nullable();
            $table->string('state')->nullable();
            
            $table->integer('client_id')->nullable()->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');
            
            $table->integer('provider_id')->nullable()->unsigned();
            $table->foreign('provider_id')->references('id')->on('providers');
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
        //
        Schema::table('address', function (Blueprint $table) {
            //
            $table->dropForeign(['client_id']);
            $table->dropForeign(['provider_id']);
        });

        Schema::dropIfExists('address');
    }
}
