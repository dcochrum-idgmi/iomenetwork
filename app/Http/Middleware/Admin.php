<?php namespace Iome\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;

// use Iome\AssignedRoles;

class Admin implements Middleware {

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
	 * @param  Guard           $auth
	 * @param  ResponseFactory $response
	 *
	 */
	public function __construct(Guard $auth, ResponseFactory $response)
	{
		$this->auth     = $auth;
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
	public function handle($request, Closure $next)
	{
		$user = $this->auth->user();
		if ( ! $user->isAdmin() )
		{
			if ( $request->ajax() )
			{
				return response('Unauthorized.', 401);
			}

			return redirect(sub_url('/', [ 'org_subdomain' => $user->organizationSlug ]));
		}

		return $next($request);
	}

}
