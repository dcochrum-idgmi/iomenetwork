<?php namespace Iome;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Iome\Office;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

	use Authenticatable, CanResetPassword;

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'email';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'email', 'password', 'fname', 'lname', 'authority', 'enabled', 'username', 'sec_email', 'address', 'city', 'state', 'zipcode', 'language', 'officeId', 'officeSlug', 'officeName' ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [ 'password',  'remember_token' ];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [ 'name' ];

	/**
	 * Indicates whether attributes are snake cased on arrays.
	 *
	 * @var bool
	 */
	public static $snakeAttributes = false;

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct( array $attributes = [] )
	{
		parent::__construct( $attributes );

		isset( $attributes[ 'exists' ] ) && $this->exists = filter_var( $attributes[ 'exists' ], FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * [isAdmin description]
	 * @return boolean [description]
	 */
	public function isAdmin()
	{
		return $this->isVendorAdmin() || $this->authority == 'ROLE_ADMIN';
	}

	/**
	 * [isVendorAdmin description]
	 * @return boolean [description]
	 */
	public function isVendorAdmin()
	{
		return $this->authority == 'ROLE_MASTER';
	}

	public static function listRoles()
	{
		return [
			'ROLE_USER' => 'User',
			'ROLE_ADMIN' => 'Admin',
			'ROLE_MASTER' => 'Master Admin'
		];
	}

//	public function setPasswordAttribute( $password )
//	{
//		$this->attributes[ 'password' ] = Hash::make( $password );
//	}

	/**
	 * @return string
	 */
	public function getNameAttribute()
	{
		return $this->attributes[ 'fname' ] . ' ' . $this->attributes[ 'lname' ];
	}

	/**
	 * Get the value of the model's route key.
	 *
	 * @return mixed
	 */
	public function getRouteKey()
	{
		return urlencode( parent::getRouteKey() );
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return $this->name;
	}

}
