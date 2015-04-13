<?php namespace Iome;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Iome\Macate\Nebula\Model;
use Nebula;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The Nebula API module for the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * Available user roles mapped to their friendly display names.
	 *
	 * @var array
	 */
	protected static $roles = [
		'ROLE_MASTER' => 'Master Admin',
		'ROLE_ADMIN'  => 'Admin',
		'ROLE_USER'   => 'User',
	];

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'username';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'username',
		'email',
		'password',
		'fname',
		'lname',
		'authority',
		'enabled',
		'address',
		'city',
		'state',
		'zipcode',
		'language',
		'organizationId',
		'organizationSlug',
		'organizationName',
		'dateEntered',
		'dateModified'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [ 'password', 'remember_token' ];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [ 'name', 'role' ];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 'bool' => 'enabled' ];


	/**
	 * [isAdmin description]
	 *
	 * @param int $organizationId
	 *
	 * @return bool [description]
	 */
	public function isAdmin($organizationId = null)
	{
		$organizationId = $organizationId ?: $this->organizationId;

		return ( $this->isMasterAdmin() || ( $this->authority == 'ROLE_ADMIN' && $this->organizationId == $organizationId ) );
	}


	/**
	 * [isMasterAdmin description]
	 * @return boolean [description]
	 */
	public function isMasterAdmin()
	{
		return $this->authority == 'ROLE_MASTER';
	}


	/**
	 * @return array
	 */
	public static function getRoles()
	{
		return static::$roles;
	}


	/**
	 * @return mixed
	 */
	public static function baseRole()
	{
		$roles = static::getRoles();
		end($roles);

		return [ key($roles) => current($roles) ];
	}


	/**
	 * @param User $other_user
	 *
	 * @return bool
	 */
	public function roleGt(User $other_user)
	{
		$roles = array_flip(array_keys(static::getRoles()));

		// Since we've ordered roles from greatest to least,
		// we need to do a reverse comparison.
		return $roles[$this->authority] < $roles[$other_user->authority];
	}


	/**
	 * @return array
	 */
	public function rolesLte()
	{
		$roles   = static::getRoles();
		$keys    = array_keys($roles);
		$indexes = array_flip($keys);

		$highest = $indexes[$this->authority];
		$keys = array_slice($indexes, $highest);

		return array_intersect_key($roles, $keys);
	}


	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array $attributes
	 *
	 * @return $this
	 *
	 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
	 */
	public function fill(array $attributes)
	{
		if ( ! isset( $this->attributes['email'] ) && ! isset( $attributes['email'] ) && ( isset( $this->attributes['username'] ) || isset( $attributes['username'] ) ) )
		{
			$attributes['email'] = isset( $attributes['username'] ) ? $attributes['username'] : $this->attributes['username'];
		}

		return parent::fill($attributes);
	}


	/**
	 * @param $password
	 */
	//public function setPasswordAttribute( $password )
	//{
	//	$username = isset( $this->attributes['username'] ) ? $this->attributes['username'] : '';
	//	$this->attributes[ 'password' ] = Nebula::hash( $username, $password );
	//}

	/**
	 * @return string
	 */
	//public function setEmailAttribute($value)
	//{
	//	dd($value);
	//	return isset( $this->attributes['email'] ) ? $this->attributes['email'] : $this->attributes['username'];
	//}

	/**
	 * @return string
	 */
	public function getNameAttribute()
	{
		$names = [ ];

		if ( isset( $this->attributes['fname'] ) )
		{
			$names[] = $this->attributes['fname'];
		}

		if ( isset( $this->attributes['lname'] ) )
		{
			$names[] = $this->attributes['lname'];
		}

		return implode(' ', $names);
	}


	/**
	 * @return string
	 */
	public function getRoleAttribute()
	{
		$roles = static::getRoles();

		return isset( $this->authority ) ? $roles[$this->authority] : null;
	}


	/**
	 * Get the value of the model's route key.
	 *
	 * @return mixed
	 */
	public function getRouteKey()
	{
		return urlencode(parent::getRouteKey());
	}


	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return $this->username;
	}

}
