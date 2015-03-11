<?php

use Illuminate\Database\Seeder;
use Iome\Office;

class OfficesTableSeeder extends Seeder
{

	public function run()
	{
		DB::table( 'offices' )->delete();

		Office::create( [ 'officeName' => 'Admin', 'officeSlug' => 'admin' ] );
		Office::create( [ 'officeName' => 'Client', 'officeSlug' => 'client' ] );
	}

}
