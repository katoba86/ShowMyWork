<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsedByPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pool_articles', function (Blueprint $table) {
            $table->foreignId('page')->default(null)->nullable(true)->constrained('pages')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pool_articles', function (Blueprint $table) {
            $table->dropColumn('page');
        });
    }
}
