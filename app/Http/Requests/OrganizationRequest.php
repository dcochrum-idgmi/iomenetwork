<?php namespace Iome\Http\Requests;

use Auth;

class OrganizationRequest extends Request {

	/**
	 * Validation rules array for store and update requests (rules
	 * ignored on destroy requests, but auth is still enforced).
	 * @var array
	 */
	protected $rules = [
		'organizationName' => 'required|min:2|nebula_unique_org',
		'slug'             => 'required|min:2|nebula_unique_org',
	];


	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$org  = is_null($this->route('orgs')) ? $this->route('org_subdomain') : $this->route('orgs');
		$user = Auth::user();

		return $user->isMasterAdmin() || ( $user->isAdmin() && $user->organizationId == $org->organizationId );
	}

}
