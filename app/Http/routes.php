<?php

use Illuminate\Support\Facades\Auth;
use Iome\Office;

Route::bind( 'offices', function ( $value ) {
	global $currentOffice;

//	$currentOffice = Nebula::getOffice( $value );
	$currentOffice = new Office( [ 'officeId' => 1, 'officeName' => 'Master', 'officeSlug' => 'admin', 'numAdmins' => 1, 'numUsers' => 2, 'numSips' => 3, 'exists' => true ] );
	View::share( 'currentOffice', $currentOffice );

	return $currentOffice;
} );

Route::bind( 'users', function ( $value ) {
	return Nebula::getUser( $value );
} );

Route::group( [ 'domain' => 'admin.' . config( 'app.domain' ), 'middleware' => [ 'auth', 'vendoradmin' ] ], function () {
	Route::get( '/', [ 'as' => 'dashboard', 'uses' => 'Admin\DashboardController@index' ] );

	route_resource_and_del( 'offices', 'Admin\OfficeController' );
} );

Route::group( [ 'domain' => '{offices}.' . config( 'app.domain' ) ], function () {
	Route::group( [ 'middleware' => [ 'auth', 'admin' ] ], function () {
		route_resource_and_del( 'exts', 'ExtensionController' );
		route_resource_and_del( 'users', 'UserController' );

		Route::get( '/', [ 'uses' => 'Admin\OfficeController@index', 'as' => 'office.index' ] );
		Route::get( 'settings', [ 'uses' => 'Admin\OfficeController@edit', 'as' => 'settings' ] );
		Route::patch( 'settings', [ 'uses' => 'Admin\OfficeController@update', 'as' => 'settings' ] );
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

	Route::get( 'css/office-custom.css', function ( Office $office ) {
		$response = Response::make( View::make( 'office.css', [ 'css' => $office->css ] ) );
		$response->header( 'Content-Type', 'text/css' );

		return $response;
	} );

} );

Route::controller( 'password', 'Auth\PasswordController', [ 'getEmail' => 'resetpw' ] );
Route::controller( '/', 'Auth\AuthController', [ 'getLogin' => 'login', 'getLogout' => 'logout', 'postLogin' => 'login.post' ] );

function route_resource_and_del( $name, $controller ) {
	Route::get( $name . '/{' . $name . '}/delete', [ 'as' => $name . '.delete', 'uses' => $controller . '@delete' ] );
	Route::resource( $name, $controller );
}

function sub_route( $route, $params = [ ], $absolute = true )
{
	merge_office_slug( $params );

	$url = URL::route( $route, $params, $absolute );

	return $url;
}

function sub_url( $url = '/', $params = [ ], $secure = false )
{
	merge_office_slug( $params );

	$url = URL::to( $url, $params, $secure ); // admin.domain.tld || admin.domain.tld
	$slug = get_current_office_slug();

	//  For some reason, we seem to create the url with {current_slug}.domain.tld/{office_slug}/abc,
	//  so let's replace the components to make the intended URL
	$url = str_replace( '/' . $params[ 'offices' ], '', $url );
	$url = str_replace( [ ':/.' . config( 'app.domain' ), '://' . get_current_office_slug() . '.' . config( 'app.domain' ) ], '://' . $params[ 'offices' ] . '.' . config( 'app.domain' ), $url );
//	//  Add back the {office_slug} but only in the subdomain position
//	$full_url = str_replace( '/' . config( 'app.domain' ), '//' . $params[ 'office_slug' ] . '.' . config( 'app.domain' ), $full_url );

	return $url;
}

/**
 * @param $params
 */
function merge_office_slug( &$params )
{
	$params = (array)$params;
	if( ! isset( $params[ 'offices' ] ) )
		$params[ 'offices' ] = get_current_office_slug();
}

function get_current_office_slug() {
	$office = get_current_office();
	return $office instanceof Office ? $office->officeSlug : 'admin';
}

function get_current_office() {
	return Route::current()->parameter( 'offices' ) ?: false;
}