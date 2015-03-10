<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'users', function ( Blueprint $table ) {
			$table->engine = 'InnoDB';
			$table->increments( 'id' );
			$table->unsignedInteger( 'organization_id' );
			$table->foreign( 'organization_id' )->references( 'id' )->on( 'organizations' )->onDelete( 'cascade' );
			$table->unsignedInteger( 'role_level' );
			$table->foreign( 'role_level' )->references( 'level' )->on( 'roles' );
			$table->string( 'first_name', 100 );
			$table->string( 'last_name', 100 );
			$table->string( 'email' )->unique();
			$table->string( 'password', 60 );
			$table->string( 'confirmation_code' );
			$table->boolean( 'confirmed' )->default( false );
			$table->rememberToken();
			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop( 'users' );
	}

}
