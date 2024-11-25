<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_articles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('article')->constrained('pool_articles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('page')->constrained('pages')->onDelete('cascade')->onUpdate('cascade');
            $table->string('slug');
            $table->string('title');
            $table->longtext('content');
            $table->text('excerpt')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_articles');
    }
}
