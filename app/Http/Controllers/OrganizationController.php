<?php namespace Iome\Http\Controllers;

use Iome\Http\Requests;
use Iome\Http\Requests\OrganizationRequest;
use Iome\Organization;
use Auth;
use Datatables;
use Nebula;
use Request;
use Route;

class OrganizationController extends Controller {

	/**
	 * @var string
	 */
	protected $route_resource = 'orgs';


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		global $currentOrg;

		if ( Request::wantsJson() )
		{
			return $this->dataTable();
		}

		$orgs = Organization::all(( $currentOrg->isMaster() && Auth::user()->isMasterAdmin() ? [ 'organizationId' => '' ] : null ));

		return view('orgs.index', compact('orgs'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$states    = $this->list_states();
		$countries = $this->list_countries();

		return view('orgs.create_edit', compact('states', 'countries'));
	}


	/**
	 * Create the specified resource in storage.
	 *
	 * @param OrganizationRequest $request
	 * @param Organization        $currentOrg
	 *
	 * @return Response
	 *
	 */
	public function store(OrganizationRequest $request, Organization $currentOrg)
	{
		$data     = $request->all();
		$org      = new Organization($data);
		$response = $org->save();
		if ( $response['success'] )
		{
			$this->flash_created();

			if ( $request->wantsJson() )
			{
				return response($org, 200);
			}

			return redirect(sub_route('orgs.index'));
		}

		if ( $request->wantsJson() )
		{
			return response([ 'status' => 'error', 'general' => $response['errorMsg'] ], 422);
		}

		return redirect(sub_route('orgs.create'))->withInput($request->all())->withErrors([ 'general' => $response['errorMsg'], ]);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param Organization $currentOrg
	 * @param Organization $org
	 *
	 * @return Response
	 */
	public function show(Organization $currentOrg, Organization $org = null)
	{
		if ( is_null($org) )
		{
			$org = $currentOrg;
		}

		return view('orgs.show', compact('org'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Organization $currentOrg
	 * @param Organization $org
	 *
	 * @return Response
	 */
	public function edit(Organization $currentOrg, Organization $org = null)
	{
		if ( is_null($org) )
		{
			$org = $currentOrg;
		}

		$states    = $this->list_states();
		$countries = $this->list_countries();

		return view('orgs.create_edit', compact('org', 'states', 'countries'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param OrganizationRequest $request
	 * @param Organization        $currentOrg
	 * @param Organization        $org
	 *
	 * @return Response
	 */
	public function update(OrganizationRequest $request, Organization $currentOrg, Organization $org = null)
	{
		$org              = $org ?: $currentOrg;
		$new_slug         = ( $request->get('slug') != $org->slug ) ?: $request->get('slug');
		$success_redirect = Route::is('settings') ? sub_route('settings',
			[ 'org_subdomain' => $new_slug ?: $org ]) : sub_route('orgs.index');
		$error_redirect   = Route::is('settings') ? sub_route('settings') : sub_route('orgs.edit', [ 'orgs' => $org ]);

		return $this->do_update($request, $org, $success_redirect, $error_redirect);
	}


	/**
	 * Confirm removal of the specified resource from storage.
	 *
	 * @param Organization $currentOrg
	 * @param Organization $org
	 *
	 * @return Response
	 */
	public function delete(Organization $currentOrg, Organization $org = null)
	{
		$org = $org ?: $currentOrg;

		return view('orgs.delete', compact('org'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param OrganizationRequest $request
	 * @param Organization        $currentOrg
	 * @param Organization        $org
	 *
	 * @return Response
	 */
	public function destroy(OrganizationRequest $request, Organization $currentOrg, Organization $org = null)
	{
		$org              = $org ?: $currentOrg;
		$success_redirect = sub_route(( Auth::user()->organizationId == $org->organizationId ? 'logout' : 'orgs.index' ));
		$error_redirect   = sub_route('orgs.edit', [ 'orgs' => $org ]);

		return $this->do_destroy($request, $org, $success_redirect, $error_redirect);
	}


	/**
	 * @param Organization $org
	 * @param array        $data
	 *
	 * @return array
	 */
	public function filterModelData(Organization $org, $data)
	{
		global $currentOrg;

		$isCurrentOrg    = $org->organizationId == $currentOrg->organizationId;
		$view_text       = trans('site.visit') . ' ' . $org->name . ' ' . strtolower(trans('site.subsite'));
		$data['actions'] = [
				'view' => '<a href="' . ( $isCurrentOrg ? '#' : sub_route('home',
						[ 'org_subdomain' => $org ]) ) . '" class="btn btn-info btn-sm iframe' . ( $isCurrentOrg ? ' disabled' : '' ) . '" title="' . $view_text . '"><span class="fa fa-eye" aria-hidden="true"></span><span class="sr-only">' . $view_text . '</span></a>'
			] + $data['actions'];

		if ( $org->organizationId == 1 )
		{
			unset( $data['actions']['delete'] );
		}

		return $data;
	}


}
