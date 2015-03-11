<?php namespace Iome\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'fname'          => 'required|min:2',
			'lname'          => 'required|min:2',
			'email'          => 'required|email|unique:users',
			'password'       => 'required|confirmed|min:5',
			'authority'      => 'required',
//			'officeId' => 'required|integer|min:1',
			'officeId' => 'required|integer',
		];
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'officeId.required' => 'You must select an office.',
		];
	}

}
