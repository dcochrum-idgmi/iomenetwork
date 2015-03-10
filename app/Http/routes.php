<?php

use Illuminate\Support\Facades\Auth;
use Iome\Organization;

Route::group( [ 'domain' => 'admin.' . config( 'app.domain' ), 'middleware' => [ 'auth', 'vendoradmin' ] ], function () {
	Route::get( '/', [ 'as' => 'dashboard', 'uses' => 'Admin\DashboardController@index' ] );

	Route::get( 'orgs/{orgs}/delete', [ 'as' => 'orgs.delete', 'uses' => 'Admin\OrganizationController@delete' ] );
	Route::resource( 'orgs', 'Admin\OrganizationController' );
} );

Route::group( [ 'domain' => '{org_slug}.' . config( 'app.domain' ) ], function () {
	Route::group( [ 'middleware' => [ 'auth', 'admin' ] ], function () {
		Route::get( 'exts/{exts}/delete', [ 'as' => 'exts.delete', 'uses' => 'ExtensionController@delete' ] );
		Route::resource( 'exts', 'ExtensionController' );

		Route::get( 'users/{users}/delete', [ 'as' => 'users.delete', 'uses' => 'UserController@delete' ] );
		Route::resource( 'users', 'UserController' );

		Route::get( '/', [ 'uses' => 'Admin\OrganizationController@index', 'as' => 'organization.index' ] );
		Route::get( 'settings', [ 'uses' => 'Admin\OrganizationController@edit', 'as' => 'settings' ] );
		Route::patch( 'settings', [ 'uses' => 'Admin\OrganizationController@update', 'as' => 'settings' ] );
	} );

	Route::group( [ 'middleware' => 'auth' ], function () {
		Route::get( '/', [ 'uses' => 'ExtensionController@index', 'as' => 'exts.show' ] );
		// Route::get('settings', ['uses' => 'ExtensionController@edit', 'as' => 'exts.edit']);
		// Route::put('settings', ['uses' => 'ExtensionController@update', 'as' => 'exts.update']);

		Route::group( [ 'prefix' => 'profile' ], function () {
			Route::get( '/', [ 'uses' => 'UserController@edit', 'as' => 'profile.edit' ] );
			Route::put( '/', [ 'uses' => 'UserController@update', 'as' => 'profile.update' ] );
			Route::patch( '/', 'UserController@update' );
			Route::get( 'delete', [ 'uses' => 'UserController@delete', 'as' => 'profile.delete' ] );
			Route::delete( '/', [ 'uses' => 'UserController@destroy', 'as' => 'profile.destroy' ] );
		} );
	} );

	Route::get( 'css/organization-custom.css', function ( Organization $org ) {
		$response = Response::make( View::make( 'organization.css', [ 'css' => $org->css ] ) );
		$response->header( 'Content-Type', 'text/css' );

		return $response;
	} );

} );

Route::controller( 'password', 'Auth\PasswordController', [ 'getEmail' => 'resetpw' ] );
Route::controller( '/', 'Auth\AuthController', [ 'getLogin' => 'login', 'getLogout' => 'logout', 'postLogin' => 'login.post' ] );

function sub_route( $route, $params = [ ], $absolute = true )
{
	merge_org_slug( $params );

	$url = URL::route( $route, $params, $absolute );

	return $url;
}

function sub_url( $url = '/', $params = [ ], $secure = false )
{
	merge_org_slug( $params );

	$url = URL::to( $url, $params, $secure ); // admin.domain.tld || admin.domain.tld
	$slug = get_current_org_slug();

	//  For some reason, we seem to create the url with {current_slug}.domain.tld/{org_slug}/abc,
	//  so let's replace the components to make the intended URL
	$url = str_replace( '/' . $params[ 'org_slug' ], '', $url );
	$url = str_replace( [ ':/.' . config( 'app.domain' ), '://' . get_current_org_slug() . '.' . config( 'app.domain' ) ], '://' . $params[ 'org_slug' ] . '.' . config( 'app.domain' ), $url );
//	//  Add back the {org_slug} but only in the subdomain position
//	$full_url = str_replace( '/' . config( 'app.domain' ), '//' . $params[ 'org_slug' ] . '.' . config( 'app.domain' ), $full_url );

	return $url;
}

/**
 * @param $params
 */
function merge_org_slug( &$params )
{
	$params = (array)$params;
	if( ! isset( $params[ 'org_slug' ] ) )
		$params[ 'org_slug' ] = get_current_org_slug();
}

function get_current_org_slug() {
	$org = get_current_org();
	return $org instanceof Organization ? $org->slug : 'admin';
}

function get_current_org() {
	return Route::current()->parameter( 'org_slug' ) ?: false;
}