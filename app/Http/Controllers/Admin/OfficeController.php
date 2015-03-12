<?php namespace Iome\Http\Controllers\Admin;

use Iome\Http\Requests;
use Iome\Http\Controllers\Controller;
use Iome\Http\Requests\Admin\OfficeCreateRequest;
use Iome\Http\Requests\Admin\OfficeEditRequest;
use Iome\Http\Requests\Admin\OfficeDeleteRequest;
use Iome\Office;
use Auth;
use Datatables;
use DB;
use Nebula;
use Request;

class OfficeController extends Controller
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

		$offices = Office::all();

		return view( 'offices.index', compact( 'offices' ) );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view( 'offices.create_edit' );
	}

	/**
	 * Create the specified resource in storage.
	 *
	 * @param OfficeCreateRequest $request
	 * @param Office              $office
	 *
	 * @return Response
	 */
	public function store( OfficeCreateRequest $request, Office $office )
	{
		$data = $request->all();
		$response = Nebula::officeCreate( $data );
		if( $response[ 'success' ] ) {
			$this->flash_created();

			if( $request->wantsJson() )
				return response( $office->fill( $data ), 200 );

			return redirect( 'offices.index' );
		}

		return response( [ 'status' => 'error', 'message' => 'Unable to save.', $response ], 422 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Office $office
	 *
	 * @return Response
	 */
	public function show( Office $office )
	{
		return view( 'offices.show', compact( 'office' ) );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  Office $office
	 *
	 * @return Response
	 */
	public function edit( Office $office )
	{
		return view( 'offices.create_edit', compact( 'office' ) );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  OfficeCreateRequest $request
	 * @param  Office     $office
	 *
	 * @return Response
	 */
	public function update( OfficeEditRequest $request, Office $office )
	{
		$new_slug = ( $request->get( 'slug' ) == $office->slug ) ? false : $request->get( 'slug' );

		$data = $request->all();
		$data[ 'officeId' ] = $office->officeId;
		$response = Nebula::officeUpdate( $data );
		if( $response[ 'success' ] ) {
			$this->flash_updated();

			if( $request->wantsJson() )
				return response( $office->fill( $data ), 200 );

			if( $new_slug )
				return redirect( sub_route( 'settings' ) );

			return redirect( 'offices.index' );
		}

		return response( [ 'status' => 'error', 'message' => 'Unable to save.', $response ], 422 );
	}

	/**
	 * Confirm removal of the specified resource from storage.
	 *
	 * @param  Office $office
	 *
	 * @return Response
	 */
	public function delete( Office $office )
	{
		return view( 'offices.delete', compact( 'office' ) );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Office $office
	 *
	 * @return Response
	 */
	public function destroy( OfficeDeleteRequest $request, Office $office )
	{
		$office->delete();

		$this->flash_deleted();

		return response( null, 204 );
	}

	/**
	 * Return a JSON list of offices formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function data()
	{
		return $this->dataTable( 'offices' );
		$cols = [ 'offices.id', 'offices.name', 'offices.slug', 'admin_count', 'active_users_count', 'total_users_count', 'extensions_count', 'offices.created_at' ];
		$sort_cols = array_values( array_diff( $cols, [ 'users.id' ] ) );
		$this->col_as_alias( $sort_cols );
		$offices = Office::select( ['*'] );
//		$offices = Office::select( $cols )
//			->leftJoin( DB::raw( '(select officeId, count(id) as admin_count from users where admin group by officeId) admin' ), function ( $join ) {
//				$join->on( 'offices.id', '=', 'admin.officeId' );
//			} )
//			->leftJoin( DB::raw( '(select officeId, count(id) as active_users_count from users where confirmed group by officeId) active_users' ), function ( $join ) {
//				$join->on( 'offices.id', '=', 'active_users.officeId' );
//			} )
//			->leftJoin( DB::raw( '(select officeId, count(id) as total_users_count from users group by officeId) users' ), function ( $join ) {
//				$join->on( 'offices.id', '=', 'users.officeId' );
//			} )
//			->leftJoin( DB::raw( '(select officeId, count(id) as extensions_count from extensions group by officeId) extensions' ), function ( $join ) {
//				$join->on( 'offices.id', '=', 'extensions.officeId' );
//			} )
//			->orderBy( $sort_cols[ Request::input( 'order.0.column', 0 ) ], Request::input( 'order.0.dir', 'asc' ) );

		$data = Datatables::of( $offices );
//			->add_column( 'actions', '<a href="{!! sub_url("/", ["office_slug" => $slug]) !!}" class="btn btn-info btn-sm" title="{!! trans("modal.view") !!}"><i class="fa fa-external-link" aria-hidden="true"></i><span class="sr-only">{!! trans("modal.view") !!}</span></a><a href="{!! route("offices.edit", $id) !!}" class="btn btn-primary btn-sm iframe" title="{!! trans("modal.edit") !!}"><i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">{!! trans("modal.edit") !!}</span></a>@if ($id != 1)<a href="{!! route("offices.delete", $id) !!}" class="btn btn-danger btn-sm iframe" title="{!! trans("modal.delete") !!}"><i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">{!! trans("modal.delete") !!}</span></a>@endif' )
//			->remove_column( 'id' );
//		$this->col_as_alias( $data->columns );

		$data->filter( function( $query ) { dd( $query ); } );

		return $data->make();
	}

}
