<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'organizations', function ( Blueprint $table ) {
			$table->increments( 'id' );
			$table->string( 'slug', 255 )->index();
			$table->string( 'name', 255 )->index();
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
		Schema::drop( 'organizations' );
	}

}
