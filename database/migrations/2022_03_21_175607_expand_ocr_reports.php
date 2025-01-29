<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandOcrReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_osr_alts', function (Blueprint $table) {
            $table->id();
            $table->integer('landing_page_id');

            $table->string('alt');
            $table->string('src');
        });
        Schema::create('reports_osr_links', function (Blueprint $table) {
            $table->id();
            $table->integer('landing_page_id');

            $table->string('name');
            $table->string('href');
            $table->string('host');
        });
        Schema::table('reports_osr_keywords', function (Blueprint $table) {
            $table->integer('heading_id')->nullable()->change();
            $table->integer('alt_id')->nullable();
            $table->integer('link_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports_osr_alts');
        Schema::dropIfExists('reports_osr_links');
        Schema::table('reports_osr_keywords', function (Blueprint $table) {
            $table->integer('heading_id')->change();
            $table->removeColumn('alt_id');
            $table->removeColumn('link_id');
        });
    }
}
