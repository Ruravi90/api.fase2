<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellingElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selling_elements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_id')->nullable()->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->integer('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('cat_products');
            $table->integer('pill_id')->nullable()->unsigned();
            $table->foreign('pill_id')->references('id')->on('cat_pills');
            $table->integer('package_id')->nullable()->unsigned();
            $table->foreign('package_id')->references('id')->on('cat_packages');
            $table->integer('service_id')->nullable()->unsigned();
            $table->foreign('service_id')->references('id')->on('cat_services');
            $table->integer('count');
            $table->float('price', 8, 2);
            $table->integer('discount');
            $table->float('total', 8, 2);
            $table->string('description')->nullable();
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
        Schema::dropIfExists('selling_elements');
    }
}
