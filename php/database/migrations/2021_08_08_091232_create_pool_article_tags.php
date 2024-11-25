<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolArticleTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pool_article_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_article_id')->constrained('pool_articles')->onDelete('cascade');
            $table->foreignId('pool_tag_id')->constrained('pool_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_article_tags');
    }
}
