<?php namespace Iome\Http\Controllers;

use Auth;
use Iome\Http\Requests;
use Iome\Http\Controllers\Controller;

use Illuminate\Http\Request;

class IndexController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		global $currentOrg;
		$user = Auth::user();

		if ( $user->isMasterAdmin() )
		{
			if ( $currentOrg->isMaster() )
			{
				return redirect(sub_route('dashboard'));
			}

			return redirect(sub_route('exts.index'));
		}

		if ( $user->isAdmin() )
		{
			return redirect(sub_route('exts.index', [ 'org_subdomain' => $user->organizationSlug ]));
		}

		return redirect(sub_route('profile.edit', [ 'org_subdomain' => $user->organizationSlug ]));
	}

}
