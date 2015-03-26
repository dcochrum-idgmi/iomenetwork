<?php namespace Iome\Http\Controllers\Admin;

use Iome\Http\Requests;
use Iome\Http\Controllers\Controller;
use Iome\Http\Requests\Admin\OrganizationCreateRequest;
use Iome\Http\Requests\Admin\OrganizationEditRequest;
use Iome\Http\Requests\Admin\OrganizationDeleteRequest;
use Iome\Organization;
use Auth;
use Datatables;
use DB;
use Nebula;
use Request;

class OrganizationController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if ( Request::wantsJson() )
		{
			return $this->dataTable('organizations');
		}

		$orgs = [ ];

		return view('orgs.index', compact('orgs'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$states    = Nebula::getStates();
		$countries = Nebula::getCountries();

		return view('orgs.create_edit', compact('states', 'countries'));
	}


	/**
	 * Create the specified resource in storage.
	 *
	 * @param OrganizationCreateRequest $request
	 * @param Organization              $org
	 *
	 * @return Response
	 */
	public function store(OrganizationCreateRequest $request, Organization $org)
	{
		$data     = $request->all();
		$response = Nebula::organizationCreate($data);
		dd($response);
		if ( $response['success'] )
		{
			$this->flash_created();

			if ( $request->wantsJson() )
			{
				return response($org->fill($data), 200);
			}

			return redirect('orgs.index');
		}

		return response([ 'status' => 'error', 'message' => 'Unable to save.', $response ], 422);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function show(Organization $org)
	{

		return view('orgs.show', compact('org'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function edit(Organization $org)
	{
		$states    = Nebula::getStates();
		$countries = Nebula::getCountries();

		return view('orgs.create_edit', compact('org', 'states', 'countries'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param OrganizationEditRequest $request
	 * @param Organization            $org
	 *
	 * @return Response
	 */
	public function update(OrganizationEditRequest $request, Organization $org)
	{
		$new_slug = ( $request->get('slug') == $org->slug ) ? false : $request->get('slug');

		$data                   = $request->except([ '_method', '_token' ]);
		$data['organizationId'] = $org->organizationId;
		$response               = Nebula::organizationUpdate($data);
		if ( $response['success'] )
		{
			$this->flash_updated();

			if ( $request->wantsJson() )
			{
				return response($org->fill($data), 200);
			}

			if ( $new_slug )
			{
				return redirect(sub_route('settings'));
			}

			return redirect('orgs.index');
		}

		return response([ 'status' => 'error', 'message' => 'Unable to save.', $response ], 422);
	}


	/**
	 * Confirm removal of the specified resource from storage.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function delete(Organization $org)
	{
		return view('orgs.delete', compact('org'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param OrganizationDeleteRequest $request
	 * @param Organization              $org
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function destroy(OrganizationDeleteRequest $request, Organization $org)
	{
		$org->delete();

		$this->flash_deleted();

		return response(null, 204);
	}

}
