<?php namespace Iome\Http\Requests\Admin;

use Iome\Http\Requests\Request;
use Auth;

class OfficeEditRequest extends Request
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
			'name' => 'required|min:3',
			'slug' => 'required|alpha_num|unique:offices,slug,' . $office->slug . ',slug|min:3|not_in:admin',
		];
	}

}
