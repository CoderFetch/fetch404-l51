<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('channel_permission', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('permission_id')->unsigned();
			$table->integer('channel_id')->unsigned();
			$table->integer('role_id')->unsigned();

			$table->timestamps();
		});
//
//		Schema::create('role_role', function(Blueprint $table)
//		{
//			$table->integer('role_id')->unsigned()->index();
//			$table->foreign('role_id')->references('id')->on('role')->onDelete('cascade');
//
//			$table->integer('role_id')->unsigned()->index();
//			$table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
//
//			$table->timestamps();
//		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('channel_permission');
	}

}
