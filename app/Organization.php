<?php namespace Iome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;

class Organization extends Model
{

	/**
	 * The vendor (master) organization.
	 *
	 * @var Organzation
	 */
//	protected static $vendor;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'id', 'officeName', 'slug', 'numAdmins', 'numUsers', 'numSips' ];

//	public static function vendor()
//	{
//		static::$vendor || static::$vendor = static::find( 1 );
//
//		return static::$vendor;
//	}

//	public function users()
//	{
//		return $this->hasMany( 'Iome\User' );
//	}
//
//	public function extensions()
//	{
//		return $this->hasMany( 'Iome\Extension' );
//	}

	public function isVendor()
	{
		return $this->id == 1;
		return $this->id === static::vendor()->id;
	}

	public function setSlugAttribute( $value )
	{
		$this->attributes[ 'slug' ] = strtolower( $value );
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
