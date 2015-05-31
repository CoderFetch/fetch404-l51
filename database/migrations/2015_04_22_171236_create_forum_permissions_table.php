<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category_permission', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('category_id')->unsigned();
			$table->integer('permission_id')->unsigned();
			$table->integer('role_id')->unsigned();

			$table->timestamps();
		});
//
//		Schema::create('category_role_role', function(Blueprint $table)
//		{
//			$table->integer('category_role_id')->unsigned()->index();
//			$table->foreign('category_role_id')->references('id')->on('category_role')->onDelete('cascade');
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
		Schema::drop('category_permission');
	}

}
