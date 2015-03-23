<?php namespace Iome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;

class Office extends Model
{
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'officeSlug';

	/**
	 * The vendor (master) office.
	 *
	 * @var Office
	 */
//	protected static $vendor;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'officeId', 'officeName', 'officeSlug', 'address', 'city', 'state', 'zipcode', 'countryId', 'language', 'numAdmins', 'numUsers', 'numSips' ];

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

//	public static function vendor()
//	{
//		static::$vendor || static::$vendor = static::find( 1 );
//
//		return static::$vendor;
//	}

	public function isVendor()
	{
		return $this->officeId == 1;
	}

	public function setOfficeSlugAttribute( $value )
	{
		$this->attributes[ 'officeSlug' ] = strtolower( $value );
	}

	public function getDataAttribute( $value )
	{
		$data = $value ? json_decode( $value, true ) : [ ];

		return new Fluent( $data );
	}

	public function setDataAttribute( $value )
	{
		$this->attributes[ 'data' ] = json_encode( $value );
	}

	public function getCssAttribute( $value )
	{
		return new Fluent( $this->data->css ?: [ ] );
	}

	public function setCssAttribute( $value )
	{
		$this->data = array_merge( $this->data->toArray(), [ 'css' => $value ] );
	}

}
