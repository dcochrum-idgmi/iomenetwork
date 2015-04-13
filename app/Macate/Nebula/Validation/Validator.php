<?php namespace Iome\Macate\Nebula\Validation;

use Illuminate\Validation\Validator as BaseValidator;
use Input;
use Iome\Extension;
use Iome\Organization;
use Iome\User;
use Nebula;

class Validator extends BaseValidator {

	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNebulaUniqueOrg($attribute, $value)
	{
		return $this->nebula_unique(Organization::getModule(), $attribute, $value);
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNebulaExistsOrg($attribute, $value)
	{
		return $this->nebula_exists(Organization::getModule(), $attribute, $value);
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNebulaUniqueUser($attribute, $value)
	{
		return $this->nebula_unique(User::getModule(), $attribute, $value);
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNebulaExistsUser($attribute, $value)
	{
		return $this->nebula_exists(User::getModule(), $attribute, $value);
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateInRoles($attribute, $value)
	{
		return array_key_exists($value, User::getRoles());
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNebulaUniqueExt($attribute, $value)
	{
		return $this->nebula_unique(Extension::getModule(), $attribute, $value);
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record already exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNebulaExistsExt($attribute, $value)
	{
		return $this->nebula_exists(Extension::getModule(), $attribute, $value);
	}


	/**
	 * Query the Nebula API against the attribute and value to ensure a matching record doesn't already exist for the
	 * module.
	 *
	 * @param string $module
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	private function nebula_unique($module, $attribute, $value)
	{
		$orig = Input::get($attribute . '_original');
		if ( $orig == $value )
		{
			return true;
		}

		return ! $this->nebula_exists($module, $value, $attribute);
	}


	/**
	 * Query the Nebula API against the attribute and value to check if a matching record exists for the module.
	 *
	 * @param string $module
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	private function nebula_exists($module, $attribute, $value)
	{
		return ! is_null(Nebula::find($module, $value, $attribute));
	}

}
