<?php

use Illuminate\Database\Seeder;
use Iome\Organization;

class OfficesTableSeeder extends Seeder
{

	public function run()
	{
		DB::table( 'offices' )->delete();

		Organization::create( [ 'officeName' => 'Admin', 'officeSlug' => 'admin' ] );
		Organization::create( [ 'officeName' => 'Client', 'officeSlug' => 'client' ] );
	}

}
