<?php namespace Iome\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use View;
use Iome\Organization;

class RouteServiceProvider extends ServiceProvider
{

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'Iome\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 *
	 * @return void
	 */
	public function boot( Router $router )
	{
		parent::boot( $router );

//		$router->model( 'exts', 'Iome\Extension' );
		$router->bind( 'org_slug', function ( $value ) {
			global $currentOrg;

//			$currentOrg = Organization::where( 'slug', $value )->first();
			$currentOrg = new Organization( [ 'id' => 1, 'name' => 'Vendor', 'slug' => 'admin', 'numAdmins' => 1, 'numUsers' => 2, 'numSips' => 3 ] );
			View::share( 'currentOrg', $currentOrg );

			return $currentOrg;
		} );
//		$router->model( 'orgs', 'Iome\Organization' );
//		$router->model( 'users', 'Iome\User' );
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 *
	 * @return void
	 */
	public function map( Router $router )
	{
		$router->group( [ 'namespace' => $this->namespace ], function ( $router ) {
			require app_path( 'Http/routes.php' );
		} );
	}

}
