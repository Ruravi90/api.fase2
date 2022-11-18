<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cat_packages',function(Blueprint $table){
             $table->integer('session_count');
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->integer('cat_package_id')->unsigned();
            $table->foreign('cat_package_id')->references('id')->on('cat_packages');
            $table->boolean('is_completed');
            $table->timestamps();
        });


        Schema::create('package_tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->foreign('package_id')->references('id')->on('packages');
            //$table->integer('user_id')->unsigned();
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->boolean('is_taken');
            $table->string('description');
            $table->dateTime('scheduled_date');
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
        Schema::table('package_tracking',function(Blueprint $table){
             $table->dropForeign(['package_id','user_id']);
        });

        Schema::dropIfExists('package_tracking');

        Schema::table('packages',function(Blueprint $table){
             $table->dropForeign(['sale_id','client_id','cat_package_id']);
        });

        Schema::dropIfExists('packages');

        Schema::table('cat_packages', function (Blueprint $table) {
            $table->dropColumn('session_count');
        });
    }
}
