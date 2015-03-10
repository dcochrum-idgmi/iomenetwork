<?php namespace Iome;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Iome\Organization;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
//	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'id', 'fname', 'lname', 'email', 'authority', 'organizationId', 'organizationSlug', 'organizationName' ];

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
//	protected $appends = [ 'full_name' ];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
//	protected $casts = [
//		'confirmed' => 'boolean',
////		'admin'     => 'boolean',
//	];

	/**
	 * Indicates if the model exists.
	 *
	 * @var bool
	 */
	public $exists = true;

	/**
	 * Indicates whether attributes are snake cased on arrays.
	 *
	 * @var bool
	 */
	public static $snakeAttributes = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
//	public function organization()
//	{
//		return $this->belongsTo( 'Iome\Organization' );
//	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
//	public function role()
//	{
//		return $this->belongsTo( 'Iome\Role' );
//	}

	/**
	 * [isMemberOf description]
	 *
	 * @param Organization $org
	 *
	 * @return bool [description]
	 */
//	public function isMemberOf( Organization $org )
//	{
//		return $this->organization->id === $org->id;
//	}

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
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return $this->fullName;
	}

}
