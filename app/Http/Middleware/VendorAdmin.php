<?php namespace Iome\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;
use Redirect;
use RedirectResponse;

// use Iome\AssignedRoles;

class VendorAdmin implements Middleware
{

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * The response factory implementation.
	 *
	 * @var ResponseFactory
	 */
	protected $response;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard          $auth
	 * @param ResponseFactory $response
	 */
	public function __construct( Guard $auth, ResponseFactory $response )
	{
		$this->auth = $auth;
		$this->response = $response;
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
		if( $this->auth->check() ) {
			if( $this->auth->user()->isVendorAdmin() )
				return $next( $request );

//			return redirect( sub_url( '/', [ 'office_slug' => $this->auth->user()->officeSlug ] ) );
		}

	}

}
