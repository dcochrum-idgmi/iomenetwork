<?php

use Illuminate\Database\Seeder;
use Iome\Organization;
use Iome\User;

class UsersTableSeeder extends Seeder
{

	public function run()
	{
		DB::table( 'users' )->delete();

		$usernames = [ 'admin', 'user' ];
		for( $i = 1; $i < 20; $i++ )
			$usernames[ ] = 'user' . $i;

		foreach( Organization::all() as $org ) {
			foreach( $usernames as $username ) {
				$username = $org->isVendor() ? $username : $username . '-' . $org->id;

				User::create( [
					'organization_id'   => $org->id,
					'first_name'        => ucfirst( $username ),
					'last_name'         => 'Last',
					'email'             => "{$username}@example.com",
					'password'          => $username,
					'confirmation_code' => md5( microtime() . Config::get( 'app.key' ) ),
					'confirmed'         => true,
					'role_level'        => starts_with( $username, 'admin' ) ? ( $org->isVendor() ? 3 : 2 ) : 1
				] );
			}
		}
	}

}
