<?php namespace Iome\Http\Controllers\Auth;

use Hash;
use Iome\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticator;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Iome\Http\Requests\Auth\LoginRequest as LoginRequest;
use Iome\Http\Requests\Auth\RegisterRequest as RegisterRequest;
use Iome\User;
use Illuminate\Http\Request;
use Nebula;

//use Request;

class AuthController extends Controller
{

	/*
	 * |--------------------------------------------------------------------------
	 * | Registration & Login Controller
	 * |--------------------------------------------------------------------------
	 * |
	 * | This controller handles the registration of new users, as well as the
	 * | authentication of existing users. By default, this controller uses
	 * | a simple trait to add these behaviors. Why don't you explore it?
	 * |
	 */

	use AuthenticatesAndRegistersUsers;

	protected $user;

	protected $loginPath = '/login';

	protected $redirectTo = '/';

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard     $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar $registrar
	 *
	 * @return void
	 */
	public function __construct( Guard $auth, Registrar $registrar )
	{
		$this->auth = $auth;
		$this->registrar = $registrar;
		$this->middleware( 'guest', [ 'except' => 'getLogout' ] );
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogout()
	{
		Nebula::logout();

		$this->auth->logout();

		return redirect( '/' );
	}

}