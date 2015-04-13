<?php namespace Iome\Macate\Nebula\Auth;

use Illuminate\Contracts\Auth\UserProvider as BaseUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Nebula;

class UserProvider implements BaseUserProvider {

	/**
	 * The hasher implementation.
	 *
	 * @var \Illuminate\Contracts\Hashing\Hasher
	 */
	protected $hasher;

	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model;


	/**
	 * Create a new database user provider.
	 *
	 * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
	 * @param  string                               $model
	 */
	public function __construct(HasherContract $hasher, $model)
	{
		$this->model  = $model;
		$this->hasher = $hasher;
	}


	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed $identifier
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveById($identifier)
	{
		if ( ! Nebula::checkSession() )
		{
			return null;
		}

		return Nebula::find('users', $identifier);
	}


	/**
	 * Retrieve a user by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string $token
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByToken($identifier, $token)
	{
		return $this->retrieveById($identifier);
	}


	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  string                                     $token
	 *
	 * @return void
	 */
	public function updateRememberToken(UserContract $user, $token)
	{
		$user->setRememberToken($token);
	}


	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array $credentials
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByCredentials(array $credentials)
	{
		return Nebula::find('users', current($credentials), key($credentials));
	}


	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  array                                      $credentials
	 *
	 * @return bool
	 */
	public function validateCredentials(UserContract $user, array $credentials)
	{
		$response = Nebula::login($credentials);

		$response['success'] && session([ 'nebulaSessionId' => $response['sessionId'] ]);

		return $response['success'];
	}

}