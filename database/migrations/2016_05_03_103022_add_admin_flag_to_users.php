<?php

use Illuminate\Database\Migrations\Migration;

class AddAdminFlagToUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('admin')->default(false)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('admin');
        });
    }
}
