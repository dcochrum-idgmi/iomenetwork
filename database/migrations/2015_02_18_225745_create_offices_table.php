<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'offices', function ( Blueprint $table ) {
			$table->increments( 'officeId' );
			$table->string( 'officeSlug', 255 )->index();
			$table->string( 'officeName', 255 );
			$table->string( 'address', 255 )->nullable();
			$table->string( 'city', 255 )->nullable();
			$table->string( 'state', 2 )->nullable();
			$table->string( 'zipcode', 5 )->nullable();
			$table->string( 'countryId', 2 )->nullable();
			$table->string( 'language', 4 )->nullable();
			$table->text( 'data' )->nullable();
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
		Schema::drop( 'offices' );
	}

}
