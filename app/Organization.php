<?php namespace Iome;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Fluent;
use Iome\Macate\Nebula\Model;
use Nebula;

class Organization extends Model {

	/**
	 * The Nebula API module for the model.
	 *
	 * @var string
	 */
	protected $table = 'organizations';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'organizationId';

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	//protected $attributes = [
	//	'numAdmins' => 1,
	//	'numUsers'  => 2,
	//	'numSips'   => 3,
	//];

	/**
	 * The master office.
	 *
	 * @var Organization
	 */
	protected static $master;

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
		'numSips',
		'dateEntered',
		'dateModified'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'organizationId' => 'int',
		'numAdmins'      => 'int',
		'numUsers'       => 'int',
		'numSips'        => 'int',
	];


	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array $attributes
	 */
	public function __construct(array $attributes = [ ])
	{
		parent::__construct($attributes);

		if ( $this->isMaster() && ! static::$master )
		{
			static::$master = $this;
		}
	}


	public static function master()
	{
		if ( ! static::$master )
		{
			static::$master = static::findOrNew(1);
		}

		return static::$master;
	}


	public function isMaster()
	{
		return $this->organizationId == 1;
	}


	public function setSlugAttribute($value)
	{
		$this->attributes['slug'] = str_slug($value);
	}


	public function getNameAttribute($value)
	{
		return $this->organizationName;
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


	/**
	 * Get the value of the model's route key.
	 *
	 * @return mixed
	 */
	public function getRouteKey()
	{
		return $this->slug;
	}

}
