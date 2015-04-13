<?php namespace Iome;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Iome\Macate\Nebula\Model;
use Illuminate\Support\Facades\Hash;
use Iome\Organization;

class Extension extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The Nebula API module for the model.
	 *
	 * @var string
	 */
	protected $table = 'sipusers';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	//protected $primaryKey = 'extension';
	protected $primaryKey = 'name';

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	//protected $attributes = [
	//	'organizationId'   => 1,
	//	'organizationName' => 'Master',
	//	'organizationSlug' => 'admin',
	//];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'extension',
		'fullname',
		'callerid',
		'secret',
		'organizationId',
		'organizationName',
		//'organizationSlug',
		'dateEntered',
		'dateModified'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [ 'secret', 'remember_token' ];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'extension' => 'string',
		'name'      => 'string',
	];

	/**
	 * [setPasswordAttribute description]
	 *
	 * @param [type] $password [description]
	 */
	//public function setPasswordAttribute( $password )
	//{
	//	$this->attributes[ 'password' ] = Hash::make( $password );
	//}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return $this->extension;
	}


	/**
	 * Get the value of the model's route key.
	 *
	 * @return mixed
	 */
	public function getRouteKey()
	{
		global $currentOrg;

		return $currentOrg->isMaster() ? $this->name : $this->extension;
	}

}
