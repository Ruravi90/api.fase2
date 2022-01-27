<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('business_name');
            $table->string('contact_name');
            $table->string('office_phone');
            $table->string('email');
            $table->timestamps();
        });

        Schema::table('address', function (Blueprint $table) {
            //
            $table->integer('creditor_id')->nullable()->unsigned();
            $table->foreign('creditor_id')->references('id')->on('creditors');
        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('address', function (Blueprint $table) {
            //
            $table->dropForeign(['creditor_id']);
        });

        Schema::dropIfExists('creditors');
    }
}
