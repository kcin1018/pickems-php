<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamPlayoffPicksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('team_playoff_picks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned();
            $table->integer('starting_points')->unsigned()->default(0);
            $table->text('picks')->nullable();
            $table->boolean('valid')->default(true);
            $table->string('reason')->nullable();

            $table->timestamps();

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
        Schema::drop('team_playoff_picks');
    }
}
