<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatPillsPillsInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pills_inventory', function (Blueprint $table) {
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pills_inventory', function (Blueprint $table) {
            $table->dropForeign(['pill_id']);
        });
    }
}
