<?php namespace Iome\Facades;

use Illuminate\Support\Facades\Facade;

class Organization extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'organization';
	}

}
