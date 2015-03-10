<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		// Add calls to Seeders here
		$this->call( 'OrganizationsTableSeeder' );
		$this->call( 'RolesTableSeeder' );
		$this->call( 'UsersTableSeeder' );
		$this->call( 'ExtensionsTableSeeder' );
		// $this->call('PermissionsTableSeeder');
		// $this->call('LanguagesTableSeeder');
	}

}