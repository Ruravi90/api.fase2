<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('credito_id')->nullable()->unsigned();
            $table->foreign('credito_id')->references('id')->on('creditors');
            $table->integer('provider_id')->nullable()->unsigned();
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->integer('purchase_id')->nullable()->unsigned();
            $table->foreign('purchase_id')->references('id')->on('purchases');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cat_product_id')->unsigned();
            $table->foreign('cat_product_id')->references('id')->on('cat_products');
            $table->integer('count')->unsigned();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            //$table->integer('user_id')->unsigned();
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->integer('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales');
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
        Schema::dropIfExists('services');

        Schema::dropIfExists('products');

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign('purchases_credito_id_foreign');
            $table->dropForeign('purchases_provider_id_foreign');
            $table->dropForeign('purchases_purchase_id_foreign');
            $table->dropColumn(['credito_id','provider_id','purchase_id']);
        });
    }
}
