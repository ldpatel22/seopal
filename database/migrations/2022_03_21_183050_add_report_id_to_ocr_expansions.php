<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportIdToOcrExpansions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_osr_alts', function (Blueprint $table) {
            $table->integer('report_id');
        });
        Schema::table('reports_osr_links', function (Blueprint $table) {
            $table->integer('report_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports_osr_alts', function (Blueprint $table) {
            $table->dropColumn('report_id');
        });
        Schema::table('reports_osr_links', function (Blueprint $table) {
            $table->dropColumn('report_id');
        });
    }
}
