<?php namespace Iome\Macate\Nebula;

use Illuminate\Support\ServiceProvider;

class NebulaServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'nebula', function () { return new NebulaAPI; } );
	}
}