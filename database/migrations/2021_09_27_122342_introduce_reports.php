<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->integer('keyword_id');
            $table->string('locale',2);
            $table->string('type');
            $table->tinyInteger('status')->default(0);
            $table->longText('data');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        Schema::create('reports_osr_domains', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id');

            $table->string('name');
            $table->integer('auth_score')->nullable();
            $table->integer('word_count')->nullable();
            $table->bigInteger('backlinks')->nullable();
        });

        Schema::create('reports_osr_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->integer('domain_id');

            $table->string('url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('auth_score')->nullable();
            $table->integer('word_count')->nullable();

            $table->longText('data')->nullable();
            $table->longText('html')->nullable();
        });

        Schema::create('reports_osr_headings', function (Blueprint $table) {
            $table->id();
            $table->integer('landing_page_id');

            $table->string('name');
            $table->tinyInteger('level');
        });

        Schema::create('reports_osr_keywords', function (Blueprint $table) {
            $table->id();
            $table->integer('heading_id');
            $table->integer('keyword_id')->nullable();

            $table->string('name');
            $table->tinyInteger('level');
            $table->integer('index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('reports_osr_domains');
        Schema::dropIfExists('reports_osr_landing_pages');
        Schema::dropIfExists('reports_osr_headings');
        Schema::dropIfExists('reports_osr_keywords');
    }
}
