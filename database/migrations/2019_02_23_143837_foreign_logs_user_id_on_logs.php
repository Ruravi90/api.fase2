<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignLogsUserIdOnLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs',function(Blueprint $table){
            $table->dropColumn(['user_id']);
        });
        Schema::table('logs',function(Blueprint $table){
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs',function(Blueprint $table){
            $table->dropForeign(['user_id']);
        });

    }
}
