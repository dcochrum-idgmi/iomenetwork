<?php namespace Iome;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Iome\Organization;

class Extension extends Model implements AuthenticatableContract, CanResetPasswordContract
{

	use Authenticatable, CanResetPassword;

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'extensions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'id', 'organization_id', 'mac', 'name', 'email', 'password' ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [ 'password', 'remember_token' ];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id'        => 'string',
		'confirmed' => 'boolean',
		'admin'     => 'boolean',
	];

	/**
	 * [organization description]
	 * @return [type] [description]
	 */
	public function organization()
	{
		return $this->belongsTo( 'Organization' );
	}

	/**
	 * [setPasswordAttribute description]
	 *
	 * @param [type] $password [description]
	 */
	public function setPasswordAttribute( $password )
	{
		$this->attributes[ 'password' ] = Hash::make( $password );
	}

	/**
	 * [isMemberOf description]
	 *
	 * @param  [type]  $org [description]
	 *
	 * @return boolean      [description]
	 */
	public function isMemberOf( $org )
	{
		return $this->organization->id === $org->id;
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return $this->id;
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array $options
	 *
	 * @return bool
	 */
	public function save( array $options = [ ] )
	{
		empty( $this->password ) && $this->setPasswordAttribute( $this->id );
		// error_log('password: '.$this->password);
		// empty($this->password) && $this->password = $this->id;
		parent::save( $options );
	}

}
