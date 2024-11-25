<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PageKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('language');
            $table->foreignId('page')->constrained('pages')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['keyword','page','language']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('page_keywords');
    }
}
