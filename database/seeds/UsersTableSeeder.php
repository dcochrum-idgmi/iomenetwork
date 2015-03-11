<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Iome\Office;
use Iome\User;

class UsersTableSeeder extends Seeder {

	public function run() {
		DB::table( 'users' )->delete();

		$usernames = [ 'admin', 'user' ];
		for( $i = 1; $i < 20; $i++ )
			$usernames[ ] = 'user' . $i;

		foreach( Office::all() as $office ) {
			foreach( $usernames as $username ) {
				$username = $office->isVendor() ? $username : $username . '-' . $office->officeId;

				User::create( [
					'officeId'          => $office->officeId,
					'fname'             => ucfirst( $username ),
					'lname'             => 'Last',
					'email'             => "{$username}@example.com",
					'password'          => Hash::make( $username ),
					'authority'         => 'ROLE_' . ( starts_with( $username, 'admin' ) ? ( $office->isVendor() ? 'MASTER' : 'ADMIN' ) : 'USER' )
				] );
			}
		}
	}

}
