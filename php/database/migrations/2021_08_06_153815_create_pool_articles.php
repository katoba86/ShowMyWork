<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pool_articles', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->timestamps();
            $table->foreignId('pool')->constrained('pool_site')->onDelete('cascade')->onUpdate('cascade');
            $table->string('slug');
            $table->string('title');
            $table->longtext('content');
            $table->text('excerpt');
            $table->json('meta');
            $table->unsignedBigInteger('iid')->nullable(false);
            $table->index(['pool','iid']);
        });

        DB::statement('ALTER TABLE pool_articles ADD FULLTEXT s1(title)');
        DB::statement('ALTER TABLE pool_articles ADD FULLTEXT s2(content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_article');
    }
}
