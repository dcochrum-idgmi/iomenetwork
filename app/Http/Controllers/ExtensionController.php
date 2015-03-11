<?php namespace Iome\Http\Controllers;

use Iome\Extension;
use Iome\Http\Requests;
use Iome\Http\Controllers\Controller;
use Iome\Office;
use Request;
use yajra\Datatables\Datatables;

class ExtensionController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$exts = Extension::all();

		if( Request::wantsJson() )
			return $this->data();

		return view( 'exts.index', compact( 'exts' ) );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show( Office $office, Extension $ext )
	{
		$title = 'Extensions';
		dd( $ext );
		$extensions = $office->extensions->toArray();

		return view( 'extensions.index', compact( $title ) )->with( 'ext', $ext );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit( $id )
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function update( $id )
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy( $id )
	{
		//
	}

	/**
	 * Show a list of all the languages posts formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function data()
	{
		global $currentOffice;

		$cols = [ 'extensions.id', 'offices.name as offices.name', 'extensions.mac', 'extensions.created_at' ];
		if( ! $currentOffice->isVendor() ) {
			$cols = array_values( array_diff( $cols, [ 'offices.name as offices.name' ] ) );
			$exts = Extension::where( 'officeId', '=', $currentOffice->id );
		} else
			$exts = Extension::join( 'offices', 'users.officeId', '=', 'offices.id' );

		$exts->select( $cols );

		$sort_cols = $cols;
		$this->col_as_alias( $sort_cols );
		$order = Request::input( 'order', [ [ 'column' => 0, 'dir' => 'asc' ] ] );
		foreach( $order as $index => $group )
			$exts->orderBy( $sort_cols[ $group[ 'column' ] ], $group[ 'dir' ] );

		$data = Datatables::of( $exts )
			->add_column( 'actions', '<a href="{!! route("exts.edit", $id) !!}" class="btn btn-primary btn-sm iframe" title="{{ trans("modal.edit") }}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><span class="sr-only">{{ trans("modal.edit") }}</span></a><a href="{!! route("exts.delete", $id) !!}" class="btn btn-sm btn-danger iframe" title="{{ trans("modal.delete") }}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span><span class="sr-only">{{ trans("modal.delete") }}</span></a>' );
		$this->col_as_alias( $data->columns );
		$data->columns = array_values( array_diff( $sort_cols, [ 'actions' ] ) );

		return $data->make();
	}

}
