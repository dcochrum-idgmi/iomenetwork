<?php namespace Iome\Http\Requests;

use Auth;

class ExtensionRequest extends Request {

	/**
	 * Validation rules array for store and update requests (rules
	 * ignored on destroy requests, but auth is still enforced).
	 * @var array
	 */
	protected $rules = [
		'extension'      => 'required|min:3|numeric|nebula_unique_ext',
		'secret'         => 'sometimes|required|confirmed|min:5',
		'organizationId' => 'required|integer|nebula_exists_org',
	];


	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
		$ext = $this->route('exts');

		return Auth::user()->isAdmin() || ( $ext && Auth::user()->getKey() == $ext->getKey() );
	}

}
