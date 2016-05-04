<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNflPlayersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nfl_players', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned();
            $table->string('gsis_id', 25)->nullable();
            $table->string('profile_id', 25);
            $table->string('name', 100);
            $table->string('position', 5);
            $table->boolean('active')->default(true);

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
        Schema::drop('nfl_players');
    }
}
