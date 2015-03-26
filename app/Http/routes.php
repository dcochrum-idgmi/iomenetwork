<?php

use Iome\Organization;

global $currentOrg;

Route::bind('org_subdomain', function ($value)
{
	global $currentOrg;

	$value == 'admin' && $value = 'macate';
	$currentOrg = Nebula::getOrganization($value, 'slug');
	View::share('currentOrg', $currentOrg);

	return $currentOrg;
});

Route::bind('orgs', function ($value)
{
	$value == 'admin' && $value = 'macate';
	$org = Nebula::getOrganization($value, 'slug');

	return $org;
});

Route::bind('users', function ($value)
{
	return Nebula::getUser($value);
});

Route::group([ 'domain' => '{org_subdomain}.' . config('app.domain') ], function ()
{
	Route::group([ 'middleware' => 'auth', 'notmaster' ], function ()
	{
		//get('{_exts?}', [ 'uses' => 'ExtensionController@index', 'as' => 'exts.index' ]);
	});
	Route::group([ 'middleware' => [ 'auth', 'masteradmin', 'master' ] ], function ()
	{
		//get('/{_dashboard?}', [ 'as' => 'dashboard', 'uses' => 'Admin\DashboardController@index' ]);
		//get('/', [ 'as' => 'dashboard', 'uses' => 'Admin\DashboardController@index' ]);
		route_resource_and_del('orgs', 'Admin\OrganizationController');
	});

	Route::group([ 'middleware' => [ 'auth', 'admin', 'notmaster' ] ], function ()
	{
		//get('/{exts.index?}', [ 'uses' => 'ExtensionController@index', 'as' => 'exts.index' ]);
	} );

	Route::group([ 'middleware' => [ 'auth', 'admin' ] ], function ()
	{
		route_resource_and_del('exts', 'ExtensionController');
		route_resource_and_del('users', 'UserController');
		get('settings', [ 'uses' => 'Admin\OrganizationController@edit', 'as' => 'settings' ]);
		patch('settings', [ 'uses' => 'Admin\OrganizationController@update', 'as' => 'settings' ]);
	});

	Route::group([ 'middleware' => 'auth' ], function ()
	{
		get('/', [ 'uses' => 'ExtensionController@index', 'as' => 'exts.index' ]);
		// get('settings', ['uses' => 'ExtensionController@edit', 'as' => 'exts.edit']);
		// put('settings', ['uses' => 'ExtensionController@update', 'as' => 'exts.update']);

		Route::group([ 'prefix' => 'profile' ], function ()
		{
			get('/', [ 'uses' => 'UserController@edit', 'as' => 'profile.edit' ]);
			put('/', [ 'uses' => 'UserController@update', 'as' => 'profile.update' ]);
			patch('/', 'UserController@update');
			get('delete', [ 'uses' => 'UserController@delete', 'as' => 'profile.delete' ]);
			delete('/', [ 'uses' => 'UserController@destroy', 'as' => 'profile.destroy' ]);
		});
	});

	get('css/org-custom.css', function (Organization $org)
	{
		$response = Response::make(View::make('org.css', [ 'css' => $org->css ]));
		$response->header('Content-Type', 'text/css');

		return $response;
	});

});

Route::controller('password', 'Auth\PasswordController', [ 'getEmail' => 'resetpw' ]);
Route::controller('/', 'Auth\AuthController',
	[ 'getLogin' => 'login', 'getLogout' => 'logout', 'postLogin' => 'login.post' ]);

function route_resource_and_del($name, $controller)
{
	get($name . '/{' . $name . '}/delete', [ 'as' => $name . '.delete', 'uses' => $controller . '@delete' ]);
	resource($name, $controller);
}

function admin_route($route, $params = [ ], $absolute = true)
{
	$params['org_subdomain'] = 'admin';

	$url = route($route, $params, $absolute);

	return $url;
}

function sub_route($route, $params = [ ], $absolute = true)
{
	merge_org_slug($params);

	$url = route($route, $params, $absolute);

	return $url;
}

function admin_url($url = '/', $params = [ ], $secure = false)
{
	$params['org_subdomain'] = 'admin';

	return url($url, $params, $secure);
}

function sub_url($url = '/', $params = [ ], $secure = false)
{
	merge_org_slug($params);

	return url($url, $params, $secure);
}

/**
 * @param $params
 */
function merge_org_slug(&$params)
{
	$params = (array) $params;
	if ( ! isset( $params['org_subdomain'] ) )
	{
		$params['org_subdomain'] = get_current_org_slug();
		$params['org_subdomain'] == 'macate' && $params['org_subdomain'] = 'admin';
	}
}

function get_current_org_slug()
{
	$org = get_current_org();

	return $org instanceof Organization ? $org->slug : 'admin';
}

function get_current_org()
{
	if ( App::runningInConsole() )
	{
		return false;
	}

	return Route::current()->parameter('org_subdomain') ?: false;
}