<?php

use Illuminate\Database\Seeder;
use Iome\Organization;
use Iome\Extension;

class ExtensionsTableSeeder extends Seeder
{

	public function run()
	{
		DB::table( 'extensions' )->truncate();

		foreach( Organization::all() as $org ) {
			for( $i = 1; $i <= 200; $i++ ) {
				$extension = Extension::create( [
					'id'              => sprintf( '%04d', $i ),
					'organization_id' => $org->id,
					'mac'             => strtoupper( implode( ':', str_split( substr( md5( mt_rand() ), 0, 12 ), 2 ) ) ), // random mac
				] );
			}
		}
	}

}
