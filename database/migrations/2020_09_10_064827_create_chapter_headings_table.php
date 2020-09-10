<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterHeadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_headings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('code_category_id');
            $table->foreign('code_category_id')->references('id')->on('code_categories')->onDelete('cascade');
            $table->string('image');
            $table->softDeletes();
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
        Schema::dropIfExists('chapter_headings');
    }
}
