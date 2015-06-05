<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgeCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badge_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('user_type', ['user', 'guest']);
            $table->enum('trigger_type', ['post', 'registration', 'likes']);
            $table->string('trigger_value')->nullable();
            $table->timestamps();
        });

        Schema::create('badge_badge_criteria', function (Blueprint $table) {
            $table->integer('badge_criteria_id')->unsigned();
            $table->integer('badge_id')->unsigned();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('badge_criteria');
        Schema::drop('badge_badge_criteria');
    }
}
