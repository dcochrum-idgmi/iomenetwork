<?php namespace Iome\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Iome\Extension;
use Organization;
use View;

class RouteServiceProvider extends ServiceProvider {

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
	public function boot(Router $router)
	{
		parent::boot($router);

		$router->bind('org_subdomain', function ($value)
		{
			global $currentOrg;

			$currentOrg = Organization::findOrNew($value, 'slug');
			View::share('currentOrg', $currentOrg);

			return $currentOrg;
		});

		$router->bind('orgs', function ($value)
		{
			return Organization::findOrFail($value, 'slug');
		});

		$router->bind('users', 'Iome\User');

		$router->bind('exts', function ($value)
		{
			global $currentOrg;

			return Extension::findOrFail($value, ( $currentOrg->isMaster() ? 'name' : 'extension' ));
		});
	}


	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 *
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group([ 'namespace' => $this->namespace ], function ($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
