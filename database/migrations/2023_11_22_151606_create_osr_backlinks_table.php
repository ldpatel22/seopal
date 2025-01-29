<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsrBacklinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_osr_backlinks', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id');
            $table->integer('landing_page_id');

            $table->string('href');
            $table->integer('auth_score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports_osr_backlinks');
    }
}
