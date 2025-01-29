<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceOsrPhrases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_osr_phrases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('report_id');
            $table->integer('planned_phrase_id')->nullable();
        });
        Schema::table('reports_osr_headings', function (Blueprint $table) {
            $table->integer('phrase_id')->nullable();
        });
        Schema::table('reports_osr_alts', function (Blueprint $table) {
            $table->integer('phrase_id')->nullable();
        });
        Schema::table('reports_osr_links', function (Blueprint $table) {
            $table->integer('phrase_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports_osr_headings', function (Blueprint $table) {
            $table->dropColumn('phrase_id');
        });
        Schema::table('reports_osr_alts', function (Blueprint $table) {
            $table->dropColumn('phrase_id');
        });
        Schema::table('reports_osr_links', function (Blueprint $table) {
            $table->dropColumn('phrase_id');
        });
        Schema::dropIfExists('reports_osr_phrases');
    }
}
