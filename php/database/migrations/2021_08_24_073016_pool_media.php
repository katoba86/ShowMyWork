<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PoolMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_media', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->id();
            $table->timestamps();
            $table->string('link');
            $table->json('infos')->nullable(true)->default(null);
            $table->foreignId('article')->constrained('page_articles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_media');
    }
}
