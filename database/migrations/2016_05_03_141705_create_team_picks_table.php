<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamPicksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('team_picks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned();
            $table->integer('week')->unsigned();
            $table->integer('number')->unsigned();
            $table->integer('stat_id')->unsigned()->nullable();
            $table->boolean('playmaker')->default(false);
            $table->boolean('valid')->default(true);
            $table->string('reason')->nullable();

            $table->timestamps();

            $table->foreign('team_id')->references('id')
                ->on('nfl_teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('stat_id')->references('id')
                ->on('nfl_stats')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('team_picks');
    }
}
