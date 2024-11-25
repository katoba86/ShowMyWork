<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolSites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pool_site', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('sourceLanguage');
            $table->string('domain');
            $table->foreignId('keyword')->nullable(true)->default(null)->constrained('page_keywords')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_site');
    }
}
