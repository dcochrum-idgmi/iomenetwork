<?php namespace Iome\Http\Requests\Admin;

use Illuminate\Validation\Factory as ValidatorFactory;
use Iome\Http\Requests\Request;
use Iome\Organization;
use Auth;

class OrgDeleteRequest extends Request
{

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize( Organization $org )
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
			'id' => 'required|not_in:1',
		];
	}

	public function validator( ValidatorFactory $factory )
	{
		$org = $this->route( 'orgs' ) ?: $this->route( 'org_slug' );
		$this->merge( [ 'id' => $org->id ] );

		return $factory->make( $this->input(), $this->rules(), $this->messages() );
	}

}
