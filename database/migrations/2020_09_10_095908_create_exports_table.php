<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_year');
            $table->unsignedBigInteger('chapter_headings');
            $table->foreign('chapter_heading_id')->references('id')->on('chapter_headings')->onDelete('cascade');
            $table->integer('code');
            $table->double('usd', 15, 2);
            $table->string('country_code');
            $table->string('coutries_code');
            $table->foreign('coutries_code')->references('code')->on('countries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exports');
    }
}
