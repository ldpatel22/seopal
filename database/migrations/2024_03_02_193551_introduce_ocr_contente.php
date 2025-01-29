<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceOcrContente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_osr_content', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id');            

            $table->longText('titles')->nullable();
            $table->longText('descriptions')->nullable();
            $table->longText('content')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports_osr_content');
    }
}
