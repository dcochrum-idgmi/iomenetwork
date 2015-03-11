<?php namespace Iome\Http\Controllers;

use Iome\Http\Requests;
use Iome\Http\Controllers\Controller;
use Iome\Office;
use Iome\User;
use Iome\Http\Requests\Admin\UserCreateRequest;
use Iome\Http\Requests\Admin\UserEditRequest;
use Iome\Http\Requests\Admin\UserDeleteRequest;
use Auth;
use Datatables;
use DB;
use Nebula;
use Request;
use Response;
use Session;

class UserController extends Controller
{

	/*
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$users = User::all();

		if( Request::wantsJson() )
			return $this->data();

		return view( 'users.index', compact( 'users' ) );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$office_id = null;
//		$offices = Office::orderBy( 'name' )->lists( 'name', 'id' );
		$offices = [ '-1' => 'Test' ];
		$roles = User::listRoles();

		return view( 'users.create_edit', compact( 'office_id', 'offices', 'roles' ) );
	}

	/**
	 * Create the specified resource in storage.
	 *
	 * @param UserCreateRequest $request
	 * @param User              $user
	 *
	 * @return Response
	 */
	public function store( UserCreateRequest $request, User $user )
	{
		$data = $request->all();
//		! $data[ 'confirmed' ] && $data[ 'confirmation_code' ] = str_random( 32 );

		// Maybe generate a random password if empty?
		// if (empty($data['password']))
		// 	unset($data['password'], $data['password_confirmation']);

		$response = Nebula::userCreate( $data );
		if( $response[ 'success' ] )
			return response( null, 200 );

		return response( [ 'status' => 'error', 'message' => 'Unable to save.', $response ], 422 );

		$user->fill( $data );
		if( $user->save() )
			return response( $user, 200 );

		return response( [ 'status' => 'error', 'message' => 'Unable to save.' ], 422 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Office $office
	 * @param User   $user
	 *
	 * @return Response
	 */
	public function show( Office $office, User $user )
	{
		Request::is( 'profile' ) && $user = Auth::user();

		if( ! $user->exists )
			abort( 404 );

		return view( 'users.show' )->with( 'user', $user );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Office $office
	 * @param User   $user
	 *
	 * @return Response
	 */
	public function edit( Office $office, User $user )
	{
		Request::is( 'profile' ) && $user = Auth::user();

		if( ! $user->exists )
			abort( 404 );

		$office_id = $user->officeId;
//		$offices = Office::orderBy( 'name' )->lists( 'name', 'id' );
		$offices = [ '-1' => 'Test' ];
		$roles = User::listRoles();

		return view( 'users.create_edit', compact( 'user', 'office_id', 'offices', 'roles' ) );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UserEditRequest $request
	 * @param User            $user
	 *
	 * @return Response
	 */
	public function update( UserEditRequest $request, User $user )
	{
		$data = $request->all();

		if( empty( $data[ 'password' ] ) )
			unset( $data[ 'password' ], $data[ 'password_confirmation' ] );
		else if( $data[ 'password' ] !== $data[ 'password_confirmation' ] )
			return response( [ 'password' => [ 'Password mismatch.' ], 'password_confirmation' => [ 'Password mismatch.' ] ], 422 );

		if( isset( $data[ 'admin' ] ) && $data[ 'admin' ] != $user->admin ) {
			if( Auth::user()->id == $user->id )
				return response( [ 'admin' => [ 'You cannot ' . ( $data[ 'admin' ] ? 'pro' : 'de' ) . 'mote yourself ' . ( $data[ 'admin' ] ? 'to' : 'from' ) . ' admin.' ] ], 422 );

			// if (! Auth::user()->isAdmin())
			// 	return response(['admin' => ['Insufficient priveleges.']], 403);
		}

		if( $user->update( $data ) )
			return response( $user, 200 );

		return response( [ 'status' => 'error', 'message' => 'Unable to save.' ], 422 );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $user
	 *
	 * @return Response
	 */

	public function delete( User $user )
	{
		return view( 'users.delete', compact( 'user' ) );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param UserDeleteRequest $request
	 * @param User              $user
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function destroy( UserDeleteRequest $request, User $user )
	{
		$user->delete();

		return response( null, 204 );
	}

	/**
	 * Show a list of all the languages posts formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function data()
	{
		global $currentOffice;

		$cols = [ 'users.id', 'users.first_name', 'users.last_name', 'offices.name as offices.name', 'users.email', 'users.confirmed', 'users.admin', 'users.created_at' ];
		if( ! $currentOffice->isVendor() ) {
			$cols = array_values( array_diff( $cols, [ 'offices.name as offices.name' ] ) );
			$users = User::where( 'officeId', '=', $currentOffice->id );
		} else
			$users = User::join( 'offices', 'users.officeId', '=', 'offices.id' );

		$users->select( $cols );

		$sort_cols = array_values( array_diff( $cols, [ 'users.id' ] ) );
		$this->col_as_alias( $sort_cols );
		$order = Request::input( 'order', [ [ 'column' => 0, 'dir' => 'asc' ] ] );
		foreach( $order as $index => $group )
			$users->orderBy( $sort_cols[ $group[ 'column' ] ], $group[ 'dir' ] );

		$data = Datatables::of( $users )
			->edit_column( 'confirmed', '<span class="glyphicon glyphicon-{{ ($confirmed) ? \'ok\' : \'remove\' }}"></span>' )
			->edit_column( 'admin', '<span class="glyphicon glyphicon-{{ ($admin) ? \'ok\' : \'remove\' }}"></span>' )
			->add_column( 'actions', '<a href="{!! route("users.edit", $id) !!}" class="btn btn-primary btn-sm iframe" title="{{ trans("modal.edit") }}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><span class="sr-only">{{ trans("modal.edit") }}</span></a>@if ($id != Auth::user()->id) <a href="{!! route("users.delete", $id) !!}" class="btn btn-sm btn-danger iframe" title="{{ trans("modal.delete") }}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span><span class="sr-only">{{ trans("modal.delete") }}</span></a> @endif' )
			->remove_column( 'id' );
		$this->col_as_alias( $data->columns );
		$data->columns = array_values( array_diff( $sort_cols, [ 'actions' ] ) );

		return $data->make();
	}

}
