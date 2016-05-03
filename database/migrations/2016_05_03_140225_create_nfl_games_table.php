<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNflGamesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nfl_games', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('week')->unsigned();
            $table->datetime('starts_at');
            $table->string('game_key', 10)->unique();
            $table->string('game_id', 15)->unique();
            $table->string('type', 5);

            $table->integer('home_team_id')->unsigned();
            $table->integer('away_team_id')->unsigned();
            $table->integer('winning_team_id')->unsigned()->nullable();
            $table->integer('losing_team_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('home_team_id')->references('id')
                ->on('nfl_teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('away_team_id')->references('id')
                ->on('nfl_teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('winning_team_id')->references('id')
                ->on('nfl_teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('losing_team_id')->references('id')
                ->on('nfl_teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nfl_games');
    }
}
