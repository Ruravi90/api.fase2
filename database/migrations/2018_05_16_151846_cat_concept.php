<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CatConcept extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_concepts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

       Schema::table('purchases', function (Blueprint $table) {
            $table->dateTime('date')->nullable();
            $table->integer('concept_id')->nullable()->unsigned();
            $table->foreign('concept_id')->references('id')->on('cat_concepts');
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
            $table->dropForeign(['concept_id']);
            $table->dropColumn(['concept_id','date']);
        });

        Schema::dropIfExists('cat_concepts');
    }
}
