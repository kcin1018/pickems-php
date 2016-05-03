<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNflStatsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nfl_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('player_id')->unsigned()->nullable();
            $table->integer('team_id')->unsigned()->nullable();
            $table->integer('week')->unsigned();
            $table->integer('td')->default(0);
            $table->integer('fg')->default(0);
            $table->integer('xp')->default(0);
            $table->integer('two')->default(0);
            $table->integer('diff')->default(0);

            $table->timestamps();

            $table->foreign('player_id')->references('id')
                ->on('nfl_players')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('team_id')->references('id')
                ->on('nfl_teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nfl_stats');
    }
}
