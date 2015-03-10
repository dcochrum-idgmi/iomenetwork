<?php namespace Iome\Http\Requests\Admin;

use Iome\Http\Requests\Request;
use Auth;

class OrgCreateRequest extends Request
{

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth::user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'officeName' => 'required|min:3',
//			'slug' => 'required|alpha_num|unique:organizations|min:3|not_in:admin',
			'slug' => 'required|alpha_num|min:3',
		];
	}

}
