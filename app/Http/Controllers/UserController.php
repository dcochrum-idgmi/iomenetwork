<?php namespace Iome\Http\Controllers;

use Iome\Http\Requests;
use Iome\Organization;
use Iome\User;
use Iome\Http\Requests\UserRequest;
use Iome\Http\Requests\UserEditRequest;
use Iome\Http\Requests\UserDeleteRequest;
use Auth;
use Datatables;
use Nebula;
use Request;
use Response;
use Route;

class UserController extends Controller {

	protected $route_resource = 'users';


	/*
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		if ( Request::wantsJson() )
		{
			return $this->dataTable();
		}

		$users = User::all($this->get_org_parameter());

		return view('users.index', compact('users'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$orgs      = $this->list_orgs();
		$roles     = Auth::user()->rolesLte();
		$base_role = key(User::baseRole());
		$states    = $this->list_states();
		$countries = $this->list_countries();

		return view('users.create_edit', compact('orgs', 'roles', 'base_role', 'states', 'countries'));
	}


	/**
	 * Create the specified resource in storage.
	 *
	 * @param UserRequest  $request
	 * @param Organization $currentOrg
	 *
	 * @return Response
	 */
	public function store(UserRequest $request, Organization $currentOrg)
	{
		$user             = new User;
		$success_redirect = sub_route('users.index');
		$error_redirect   = sub_route('users.create');

		return $this->do_create($request, $user, $success_redirect, $error_redirect);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param Organization $currentOrg
	 * @param User         $user
	 *
	 * @return Response
	 */
	public function show(Organization $currentOrg, User $user)
	{
		Request::is('profile') && $user = Auth::user();

		return view('users.show')->with('user', $user);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Organization $currentOrg
	 * @param User         $user
	 *
	 * @return Response
	 */
	public function edit(Organization $currentOrg, User $user)
	{
		Request::is('profile') && $user = Auth::user();

		$orgs      = $this->list_orgs();
		$roles     = Auth::user()->rolesLte();
		$base_role = key(User::baseRole());
		$states    = $this->list_states();
		$countries = $this->list_countries();

		return view('users.create_edit', compact('user', 'orgs', 'roles', 'base_role', 'states', 'countries'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param UserRequest  $request
	 * @param Organization $currentOrg
	 * @param User         $user
	 *
	 * @return Response
	 */
	public function update(UserRequest $request, Organization $currentOrg, User $user = null)
	{
		$user             = $user ?: Auth::user();
		$success_redirect = sub_route(( Route::is('profile') ? 'profile' : 'users.index' ));
		$error_redirect   = Route::is('profile') ? sub_route('profile') : sub_route('users.edit', [ 'users' => $user ]);

		return $this->do_update($request, $user, $success_redirect, $error_redirect);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Organization $currentOrg
	 * @param User         $user
	 *
	 * @return Response
	 */

	public function delete(Organization $currentOrg, User $user)
	{
		return view('users.delete', compact('user'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param UserRequest  $request
	 * @param Organization $currentOrg
	 * @param User         $user
	 *
	 * @return Response
	 */
	public function destroy(UserRequest $request, Organization $currentOrg, User $user)
	{
		$success_redirect = sub_route('users.index');
		$error_redirect   = sub_route('users.delete', [ 'users' => $user ]);

		return $this->do_destroy($request, $user, $success_redirect, $error_redirect);
	}


	/**
	 * @param User  $user
	 * @param array $data
	 *
	 * @return array
	 */
	public function filterModelData(User $user, $data)
	{
		if ( $user->roleGt(Auth::user()) )
		{
			$data['actions'] = [ ];
		}

		if ( $user->username == Auth::user()->username )
		{
			unset( $data['actions']['delete'] );
		}

		return $data;
	}

}
