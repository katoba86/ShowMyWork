<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pageserps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_serps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();


            $table->string('keyword');
            $table->string('link');
            $table->text('snippet')->nullable(true);
            $table->string('title')->nullable(false);
            $table->text('description')->nullable(true);
            $table->string('locale')->nullable(true);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_serps');
    }
}
