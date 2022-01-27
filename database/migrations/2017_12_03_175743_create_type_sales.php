<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_type_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('sales',function(Blueprint $table){
            $table->integer('type_sale_id')->nullable()->unsigned();
            $table->foreign('type_sale_id')->references('id')->on('cat_type_sales');
        });

    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales',function(Blueprint $table){
            $table->dropForeign(['type_sale_id']);
            $table->dropColumn('type_sale_id');
        });

        Schema::dropIfExists('cat_type_sales');
    }
}
