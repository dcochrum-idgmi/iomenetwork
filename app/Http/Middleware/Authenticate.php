<?php namespace Iome\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Iome\Macate\Nebula\Nebula;

class Authenticate
{

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard $auth
	 */
	public function __construct( Guard $auth )
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle( $request, Closure $next )
	{
		if( $this->auth->check() && ! Nebula::checkSession() )
			$this->auth->logout();

		if( $this->auth->guest() ) {
			if( $request->ajax() )
				return response( 'Unauthorized.', 401 );
			else
				return redirect()->guest( 'login' );
		}

//		dd( $this->auth->user());

		return $next( $request );
	}

}
