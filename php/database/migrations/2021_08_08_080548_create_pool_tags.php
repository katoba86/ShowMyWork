<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pool_tags', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('iid')->nullable(false);
            $table->timestamps();
            $table->string('slug')->nullable(false);
            $table->text('description')->nullable(true);
            $table->text('name')->nullable(false);
            $table->enum('language',['de','en','fr','ch','ru','nl','es']);
            $table->foreignId('pool')->constrained('pool_site')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['iid','pool']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
