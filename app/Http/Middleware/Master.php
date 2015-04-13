<?php namespace Iome\Http\Middleware;

use Auth;
use Closure;
use Route;

class Master {

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
		global $currentOrg;

		if ( ! $currentOrg->isMaster() )
		{
			$user = Auth::user();
			if ( $user->isMasterAdmin() )
			{
				return redirect(admin_route(Route::current()->getName()));
			}

			if ( $request->wantsJson() )
			{
				return response('Unauthorized.', 401);
			}

			return redirect(sub_route('home', [ 'org_subdomain' => $user->organizationSlug ]));
		}

		return $next($request);
	}

}
