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
			$table->unsignedInteger( 'officeId' );
			$table->foreign( 'officeId' )->references( 'officeId' )->on( 'offices' )->onDelete( 'cascade' );
			$table->text( 'authority', 50 );
			$table->string( 'fname', 100 );
			$table->string( 'lname', 100 );
			$table->string( 'email' )->unique();
			$table->string( 'password', 60 );
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
