<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceOcrPhrasesLandingPagesLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osr_phrases_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->integer('phrase_id');
            $table->integer('landing_page_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osr_phrases_landing_pages');
    }
}
