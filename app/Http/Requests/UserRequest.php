<?php namespace Iome\Http\Requests;

use Auth;

class UserRequest extends Request {

	/**
	 * Validation rules array for store and update requests (rules
	 * ignored on destroy requests, but auth is still enforced).
	 * @var array
	 */
	protected $rules = [
		'fname'          => 'required|min:2',
		'lname'          => 'required|min:2',
		'username'       => 'required|email|nebula_unique_user',
		'password'       => 'sometimes|required|confirmed|min:5',
		'authority'      => 'sometimes|required|in_roles',
		'organizationId' => 'required|integer|nebula_exists_org',
	];


	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = $this->route('users');

		return Auth::user()->isAdmin() || ( $user && Auth::user()->getKey() == $user->getKey() );
	}

}
