<?php namespace Iome\Macate\Nebula;

use Illuminate\Support\Facades\Facade;

class Nebula extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'nebula';
	}

}
