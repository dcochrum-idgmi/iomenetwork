<?php namespace Iome\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UserEditRequest extends FormRequest
{

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name'      => 'required|min:3',
			'authority' => 'required',
		];
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return ( Auth::user()->isAdmin() || Auth::user()->id == $this->user->id );
	}

}
