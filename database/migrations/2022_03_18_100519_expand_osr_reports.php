<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandOsrReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_osr_domains', function (Blueprint $table) {
            $table->bigInteger('backlinks_domains')->nullable();
            $table->bigInteger('backlinks_urls')->nullable();
        });
        Schema::table('reports_osr_landing_pages', function (Blueprint $table) {
            $table->bigInteger('backlinks_domains')->nullable();
            $table->bigInteger('backlinks_urls')->nullable();
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
            $table->removeColumn('backlinks_domains');
            $table->removeColumn('backlinks_urls');
        });
        Schema::table('reports_osr_landing_pages', function (Blueprint $table) {
            $table->removeColumn('backlinks_domains');
            $table->removeColumn('backlinks_urls');
        });
    }
}
