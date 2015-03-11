<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtensionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'extensions', function ( Blueprint $table ) {
			$table->engine = 'InnoDB';
			$table->string( 'id', 4 )->index();
			$table->unsignedInteger( 'officeId' );
			$table->foreign( 'officeId' )->references( 'officeId' )->on( 'offices' )->onDelete( 'cascade' );
			$table->string( 'mac', 17 );
			$table->string( 'name', 100 )->nullable();
			$table->string( 'email' )->nullable()->unique();
			$table->string( 'password', 60 );
			$table->string( 'remember_token' )->nullable();
			$table->timestamps();
			$table->unique( [ 'id', 'officeId' ] );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop( 'extensions' );
	}

}
