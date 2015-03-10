<?php

use Iome\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

	public function run()
	{
		DB::table( 'roles' )->delete();

		$roles = [ '1' => 'ROLE_USER', '2' => 'ROLE_ADMIN', '3' => 'ROLE_MASTER' ];
		foreach( $roles as $level => $name )
			Role::create( [ 'level' => $level, 'name' => $name ] );
	}

}
