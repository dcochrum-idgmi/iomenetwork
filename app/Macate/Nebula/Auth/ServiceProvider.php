<?php namespace Iome\Macate\Nebula\Auth;

use Illuminate\Auth\Guard;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\UserProviderInterface;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Iome\Macate\Nebula\Auth\UserProvider;

class ServiceProvider extends BaseServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot Provider
	 */
	public function boot()
	{

//		$this->package( 'nebula/auth' );

		$this->app[ 'auth' ]->extend( 'nebula', function ( $app ) {
			$model = config( 'auth.model' );
			$provider = new UserProvider( new BcryptHasher, $model );

			return new Guard( $provider, $app[ 'session.store' ] );
		} );

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [ 'auth.nebula' ];
	}

}