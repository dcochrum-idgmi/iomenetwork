<?php

use Illuminate\Database\Seeder;
use Iome\Organization;

class OrganizationsTableSeeder extends Seeder
{

	public function run()
	{
		DB::table( 'organizations' )->delete();

		Organization::create( [ 'name' => 'Admin', 'slug' => 'admin' ] );
		Organization::create( [ 'name' => 'Client', 'slug' => 'client' ] );
	}

}
