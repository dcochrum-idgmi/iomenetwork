<?php namespace Iome\Http\Requests\Admin;

use Iome\Http\Requests\Request;
use Auth;

class UserDeleteRequest extends Request
{

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return ( Auth::user()->isAdmin() || Auth::user()->id == $this->user->id );
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			//
		];
	}

}
