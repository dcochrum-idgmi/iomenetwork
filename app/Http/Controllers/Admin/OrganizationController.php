<?php namespace Iome\Http\Controllers\Admin;

use Iome\Http\Requests;
use Iome\Http\Controllers\Controller;
use Iome\Http\Requests\Admin\OrgCreateRequest;
use Iome\Http\Requests\Admin\OrgEditRequest;
use Iome\Http\Requests\Admin\OrgDeleteRequest;
use Iome\Organization;
use Auth;
use Datatables;
use DB;
use Request;

class OrganizationController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if( Request::wantsJson() )
			return $this->data();

		$orgs = Organization::all();

		return view( 'orgs.index', compact( 'orgs' ) );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view( 'orgs.create_edit' );
	}

	/**
	 * Create the specified resource in storage.
	 *
	 * @param $org
	 *
	 * @return Response
	 */
	public function store( OrgCreateRequest $request, Organization $org )
	{
		$org->fill( $request->all() );
		if( $org->save() ) {
			$this->flash_created();

			return response( $org, 200 );
		}

		return response( [ 'status' => 'error', 'message' => 'Unable to save.' ], 422 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function show( Organization $org )
	{
		return view( 'orgs.show', compact( 'org' ) );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function edit( Organization $org )
	{
		return view( 'orgs.create_edit', compact( 'org' ) );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  OrgCreateRequest $request
	 * @param  Organization     $org
	 *
	 * @return Response
	 */
	public function update( OrgEditRequest $request, Organization $org )
	{
		$new_slug = ( $request->get( 'slug' ) == $org->slug ) ? false : $request->get( 'slug' );

		if( $org->update( $request->all() ) ) {
			$this->flash_updated();

			if( Request::wantsJson() )
				return response( $org, 200 );

			if( $new_slug )
				return redirect( sub_route( 'settings' ) );

			return redirect( 'settings' );
		}

		if( $request->wantsJson() )
			return response( [ 'status' => 'error', 'message' => 'Unable to save.' ], 422 );
	}

	/**
	 * Confirm removal of the specified resource from storage.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function delete( Organization $org )
	{
		return view( 'orgs.delete', compact( 'org' ) );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Organization $org
	 *
	 * @return Response
	 */
	public function destroy( OrgDeleteRequest $request, Organization $org )
	{
		$org->delete();

		$this->flash_deleted();

		return response( null, 204 );
	}

	/**
	 * Return a JSON list of organizations formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function data()
	{
		$cols = [ 'organizations.id', 'organizations.name', 'organizations.slug', 'admin_count', 'active_users_count', 'total_users_count', 'extensions_count', 'organizations.created_at' ];
		$sort_cols = array_values( array_diff( $cols, [ 'users.id' ] ) );
		$this->col_as_alias( $sort_cols );
		$orgs = Organization::select( $cols )
			->leftJoin( DB::raw( '(select organization_id, count(id) as admin_count from users where admin group by organization_id) admin' ), function ( $join ) {
				$join->on( 'organizations.id', '=', 'admin.organization_id' );
			} )
			->leftJoin( DB::raw( '(select organization_id, count(id) as active_users_count from users where confirmed group by organization_id) active_users' ), function ( $join ) {
				$join->on( 'organizations.id', '=', 'active_users.organization_id' );
			} )
			->leftJoin( DB::raw( '(select organization_id, count(id) as total_users_count from users group by organization_id) users' ), function ( $join ) {
				$join->on( 'organizations.id', '=', 'users.organization_id' );
			} )
			->leftJoin( DB::raw( '(select organization_id, count(id) as extensions_count from extensions group by organization_id) extensions' ), function ( $join ) {
				$join->on( 'organizations.id', '=', 'extensions.organization_id' );
			} )
			->orderBy( $sort_cols[ Request::input( 'order.0.column', 0 ) ], Request::input( 'order.0.dir', 'asc' ) );

		$data = Datatables::of( $orgs )
			->add_column( 'actions', '<a href="{!! sub_url("/", ["org_slug" => $slug]) !!}" class="btn btn-info btn-sm" title="{!! trans("modal.view") !!}"><i class="fa fa-external-link" aria-hidden="true"></i><span class="sr-only">{!! trans("modal.view") !!}</span></a><a href="{!! route("orgs.edit", $id) !!}" class="btn btn-primary btn-sm iframe" title="{!! trans("modal.edit") !!}"><i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">{!! trans("modal.edit") !!}</span></a>@if ($id != 1)<a href="{!! route("orgs.delete", $id) !!}" class="btn btn-danger btn-sm iframe" title="{!! trans("modal.delete") !!}"><i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">{!! trans("modal.delete") !!}</span></a>@endif' )
			->remove_column( 'id' );
		$this->col_as_alias( $data->columns );

		return $data->make();
	}

}
