<?php namespace Iome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Language extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'language';


	/**
	 * The attributes included in the model's JSON form.
	 *
	 * @var array
	 */
	protected $fillable = [ 'name', 'lang_code', 'description', 'icon' ];

	/**
	 * The rules for email field, automatic validation.
	 *
	 * @var array
	 */
	private $rules = [
		'name'      => 'required|min:2',
		'lang_code' => 'required|min:2'
	];

	public function getImageUrl( $withBaseUrl = false )
	{
		if( ! $this->icon ) return null;

		$imgDir = '/images/languages/' . $this->id;
		$url = $imgDir . '/' . $this->icon;

		return $withBaseUrl ? URL::asset( $url ) : $url;
	}
}