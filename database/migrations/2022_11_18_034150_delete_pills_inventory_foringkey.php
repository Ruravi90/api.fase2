<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selling_elements', function (Blueprint $table) {
            $table->dropForeign('selling_elements_pill_id_foreign');
            $table->dropColumn('pill_id');
        });

        Schema::table('sale_additionals', function (Blueprint $table) {
            $table->dropForeign('sale_additionals_pill_id_foreign');
            $table->dropColumn('pill_id');
        });

        Schema::table('complements_packages',function(Blueprint $table){
            $table->dropForeign('complements_packages_pill_id_foreign');
            $table->dropColumn('pill_id');
        });

        Schema::table('sales',function(Blueprint $table){
            $table->dropForeign('sales_pill_id_foreign');
            $table->dropColumn('pill_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign('purchases_pill_id_foreign');
            $table->dropColumn('pill_id');
        });

        Schema::table('pills_inventory', function (Blueprint $table) {
            $table->dropForeign('pills_inventory_pill_id_foreign');
            $table->dropColumn('pill_id');
        });

        Schema::dropIfExists('pills_inventory');
        Schema::dropIfExists('cat_pills');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('cat_pills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->float('price', 8, 2);
            $table->timestamps();
        });

        Schema::create('pills_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('count');
            $table->timestamps();
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });
        Schema::table('sales',function(Blueprint $table){
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });
        Schema::table('complements_packages',function(Blueprint $table){
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });
        Schema::table('sale_additionals', function (Blueprint $table) {
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });

        Schema::table('selling_elements', function (Blueprint $table) {
            $table->integer('pill_id')->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
        });
    }
};
