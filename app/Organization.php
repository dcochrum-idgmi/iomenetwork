<?php namespace Iome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;

class Organization extends Model {

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'slug';

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	protected $attributes = [ 'numAdmins'   => 1,
	                          'numUsers'    => 2,
	                          'numSips'     => 3,
	                          'dateEntered' => '2015-03-25 00:00:00'
	];

	/**
	 * The vendor (master) office.
	 *
	 * @var Organization
	 */
//	protected static $master;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'organizationId',
		'organizationName',
		'slug',
		'address',
		'city',
		'state',
		'zipcode',
		'countryId',
		'language',
		'numAdmins',
		'numUsers',
		'numSips'
	];

	/**
	 * Indicates whether attributes are snake cased on arrays.
	 *
	 * @var bool
	 */
	public static $snakeAttributes = false;

	/**
	 * The name of the "created at" column.
	 *
	 * @var string
	 */
	const CREATED_AT = 'dateEntered';

	/**
	 * The name of the "updated at" column.
	 *
	 * @var string
	 */
	const UPDATED_AT = 'dateModified';

//	public static function master()
//	{
//		static::$master || static::$master = static::find( 1 );
//
//		return static::$master;
//	}

	public function isMaster()
	{
		return $this->organizationId == 1;
	}


	public function setSlugAttribute($value)
	{
		$this->attributes['slug'] = str_replace('.' . config('app.domain'), '', strtolower($value));
	}


	public function getDataAttribute($value)
	{
		$data = $value ? json_decode($value, true) : [ ];

		return new Fluent($data);
	}


	public function setDataAttribute($value)
	{
		$this->attributes['data'] = json_encode($value);
	}


	public function getCssAttribute($value)
	{
		return new Fluent($this->data->css ?: [ ]);
	}


	public function setCssAttribute($value)
	{
		$this->data = array_merge($this->data->toArray(), [ 'css' => $value ]);
	}

}
