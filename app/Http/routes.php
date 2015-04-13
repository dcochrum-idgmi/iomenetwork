<?php

use Iome\Organization;

Route::group([ 'domain' => '{org_subdomain}.' . config('app.domain') ], function ()
{
	Route::group([ 'middleware' => [ 'auth', 'master' ] ], function ()
	{
		get('dashboard', [ 'as' => 'dashboard', 'uses' => 'DashboardController@index' ]);
		route_resource_and_del('orgs', 'OrganizationController');
	});

	Route::group([ 'middleware' => [ 'auth', 'admin' ] ], function ()
	{
		route_resource_and_del('exts', 'ExtensionController');
		route_resource_and_del('users', 'UserController');
		get('settings', [ 'uses' => 'OrganizationController@edit', 'as' => 'settings' ]);
		patch('settings', [ 'uses' => 'OrganizationController@update', 'as' => 'settings' ]);
	});

	Route::group([ 'middleware' => 'auth' ], function ()
	{
		Route::any('/', [ 'as' => 'home', 'uses' => 'IndexController@index' ]);

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
	$params['org_subdomain'] = Organization::master()->slug;

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
	$params['org_subdomain'] = Organization::master()->slug;

	return url($url, $params, $secure);
}

function sub_url($url = '/', $params = [ ], $secure = false)
{
	merge_org_slug($params);

	return url($url, $params, $secure);
}

function sub_action($action, $params = [ ])
{
	merge_org_slug($params);

	return action($action, $params);
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
	}
}

function get_current_org_slug()
{
	$org = get_current_org();

	return $org instanceof Organization ? $org->slug : Organization::master()->slug;
}

function get_current_org()
{
	if ( App::runningInConsole() )
	{
		return false;
	}

	return Route::current()->parameter('org_subdomain') ?: false;
}

function current_org_is_master()
{
	$org = get_current_org();

	return ! $org instanceof Organization ?: $org->isMaster();
}