<?php namespace Iome\Http\Controllers;

use Iome\Extension;
use Iome\Http\Requests;
use Iome\Http\Requests\ExtensionRequest;
use Iome\Organization;
use Nebula;
use Request;

class ExtensionController extends Controller {

	/**
	 * @var string
	 */
	protected $route_resource = 'exts';


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$exts = Extension::all();

		if ( Request::wantsJson() )
		{
			return $this->dataTable();
		}

		return view('exts.index', compact('exts'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$orgs      = $this->list_orgs();
		$states    = $this->list_states();
		$countries = $this->list_countries();

		return view('exts.create_edit', compact('orgs', 'states', 'countries'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param ExtensionRequest $request
	 * @param Organization     $currentOrg
	 *
	 * @return Response
	 */
	public function store(ExtensionRequest $request, Organization $currentOrg)
	{
		$ext              = new Extension;
		$success_redirect = sub_route('exts.index');
		$error_redirect   = sub_route('exts.create');

		return $this->do_create($request, $ext, $success_redirect, $error_redirect);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param Organization $currentOrg
	 * @param Extension    $ext
	 *
	 * @return Response
	 */
	public function show(Organization $currentOrg, Extension $ext)
	{
		$title = 'Extension';

		return view('exts.index', compact('title', 'ext'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Organization $currentOrg
	 * @param Extension    $ext
	 *
	 * @return Response
	 */
	public function edit(Organization $currentOrg, Extension $ext)
	{
		$orgs      = $this->list_orgs();
		$states    = $this->list_states();
		$countries = $this->list_countries();

		return view('exts.create_edit', compact('ext', 'orgs', 'states', 'countries'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param ExtensionRequest $request
	 * @param Organization     $currentOrg
	 * @param Extension        $ext
	 *
	 * @return Response
	 */
	public function update(ExtensionRequest $request, Organization $currentOrg, Extension $ext)
	{
		//$ext             = $ext ?: Auth::user();
		$success_redirect = sub_route('exts.index');
		$error_redirect   = sub_route('exts.edit', [ 'exts' => $ext ]);

		return $this->do_update($request, $ext, $success_redirect, $error_redirect);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Organization $currentOrg
	 * @param Extension    $ext
	 *
	 * @return Response
	 */

	public function delete(Organization $currentOrg, Extension $ext)
	{
		return view('exts.delete', compact('ext'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param ExtensionRequest $request
	 * @param Organization     $currentOrg
	 * @param Extension        $ext
	 *
	 * @return Response
	 */
	public function destroy(ExtensionRequest $request, Organization $currentOrg, Extension $ext)
	{
		$success_redirect = sub_route('exts.index');
		$error_redirect   = sub_route('exts.delete', [ 'exts' => $ext ]);

		return $this->do_destroy($request, $ext, $success_redirect, $error_redirect);
	}


	/**
	 * @param Extension $ext
	 * @param array     $data
	 *
	 * @return array
	 */
	//public function filterModelData(Extension $ext, $data)
	//{
	//	global $currentOrg;
	//
	//	$isCurrentOrg    = $ext->organizationId == $currentOrg->organizationId;
	//	$view_text       = trans('site.visit') . ' ' . $ext->name . ' ' . strtolower(trans('site.subsite'));
	//	$data['actions'] = [
	//			'view' => '<a href="' . ( $isCurrentOrg ? '#' : sub_route('home',
	//					[ 'org_subdomain' => $ext ]) ) . '" class="btn btn-info btn-sm iframe' . ( $isCurrentOrg ? ' disabled' : '' ) . '" title="' . $view_text . '"><span class="fa fa-eye" aria-hidden="true"></span><span class="sr-only">' . $view_text . '</span></a>'
	//		] + $data['actions'];
	//
	//	if ( $ext->organizationId == 1 )
	//	{
	//		unset( $data['actions']['delete'] );
	//	}
	//
	//	return $data;
	//}

}
