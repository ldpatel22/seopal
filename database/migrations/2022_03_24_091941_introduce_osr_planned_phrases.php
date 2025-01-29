<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceOsrPlannedPhrases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_osr_planned_phrases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('report_id');
            $table->integer('phrase_id');
            $table->integer('keyword_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports_osr_planned_phrases');
    }
}
