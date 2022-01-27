<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplementsPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('complements_packages');
        Schema::create('complements_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->nullable()->unsigned();
            $table->foreign('package_id')->references('id')->on('cat_packages');
            $table->integer('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('cat_products');
            $table->integer('pill_id')->nullable()->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');

            $table->integer('count')->nullable()->unsigned();
            $table->timestamps();
        });

        Schema::table('cat_packages', function (Blueprint $table) {
            $table->dropForeign(['pill_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id','product_count','pill_id','pill_count']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cat_packages',function(Blueprint $table){
            $table->integer('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('cat_products');
            $table->integer('product_count')->nullable()->unsigned();
            $table->integer('pill_id')->nullable()->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
            $table->integer('pill_count')->nullable()->unsigned();
        });

        Schema::dropIfExists('complements_packages');
    }
}
