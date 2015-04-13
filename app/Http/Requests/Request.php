<?php namespace Iome\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest {

	/**
	 * Validation rules array for store and update requests (rules
	 * ignored on destroy requests, but auth is still enforced).
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		if ( ends_with($this->route()->getActionName(), 'destroy' ) )
		{
			return [ ];
		}

		return $this->rules;
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

}
