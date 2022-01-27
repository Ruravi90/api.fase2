<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('cat_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

       Schema::table('purchases', function (Blueprint $table) {
            $table->integer('expence_id')->nullable()->unsigned();
            $table->foreign('expence_id')->references('id')->on('cat_expenses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['expence_id']);
            $table->dropColumn(['expence_id']);
        });

        Schema::dropIfExists('cat_expenses');
    }
}
