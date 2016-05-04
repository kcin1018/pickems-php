<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNflTeamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nfl_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('abbr', 5)->unique();
            $table->string('conference', 5);
            $table->string('city', 50);
            $table->string('name', 50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nfl_teams');
    }
}
