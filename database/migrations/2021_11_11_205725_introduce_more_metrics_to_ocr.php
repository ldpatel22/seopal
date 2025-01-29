<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceMoreMetricsToOcr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_osr_domains', function (Blueprint $table) {
            $table->bigInteger('rank')->nullable();
        });
        Schema::table('reports_osr_landing_pages', function (Blueprint $table) {
            $table->bigInteger('backlinks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports_osr_domains', function (Blueprint $table) {
            $table->removeColumn('rank');
        });
        Schema::table('reports_osr_landing_pages', function (Blueprint $table) {
            $table->removeColumn('backlinks');
        });
    }
}
