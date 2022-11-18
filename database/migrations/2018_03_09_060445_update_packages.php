<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales',function(Blueprint $table){
             //$table->integer('responsible_id')->nullable()->unsigned();
             $table->foreignId('responsible_id')->nullable()->references('id')->on('users');
        });

        Schema::table('cat_packages',function(Blueprint $table){
            $table->integer('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('cat_products');
            $table->integer('product_count')->nullable()->unsigned();
            $table->integer('pill_id')->nullable()->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
            $table->integer('pill_count')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('sales_user_id_foreign');
            $table->dropColumn(['user_id']);
        });

        Schema::table('cat_packages', function (Blueprint $table) {
            $table->dropForeign('cat_packages_pill_id_foreign');
            $table->dropForeign('cat_packages_product_id_foreign');
            $table->dropColumn(['product_id','product_count','pill_id','pill_count']);
        });
    }
}
