<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntroduceKeywordStatsDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('keyword_stats');
        Schema::create('keyword_stats', function (Blueprint $table) {
            $table->id();
            $table->string('entity');
            $table->string('locale',2)->nullable();
            $table->longText('data')->nullable();
            $table->timestamps();

            $table->index(['entity', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keyword_stats');
    }
}
