<?php namespace Iome\Http\Requests\Admin;

use Iome\Http\Requests\Request;
use Auth;

class OrganizationEditRequest extends Request
{

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth::check() && Auth::user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$office = $this->route( 'offices' ) ?: $this->route( 'office_slug' );

		return [
			'officeName' => 'required|min:3',
			'officeSlug' => 'required|alpha_num|min:3',
		];
	}

}
