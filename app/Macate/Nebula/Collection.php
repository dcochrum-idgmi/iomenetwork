<?php namespace Iome\Macate\Nebula;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection {

	/**
	 * The total items matched in the collection.
	 *
	 * @var int
	 */
	protected $total = 0;

	/**
	 * The error message returned when attempting to retrive the items.
	 *
	 * @var string
	 */
	protected $error;


	/**
	 * Create a new collection.
	 *
	 * @param mixed  $items
	 * @param int    $total
	 * @param string $error
	 */
	public function __construct($items = array(), $total = null, $error = null)
	{
		parent::__construct($items);

		$this->total = $total ?: $this->count();
		$this->error = $error;
	}


	/**
	 * Return the number of items matched in the collection.
	 *
	 * @return int
	 */
	public function total()
	{
		return $this->total;
	}


	/**
	 * Return the error message.
	 *
	 * @return string
	 */
	public function error()
	{
		return $this->error;
	}


	/**
	 * Return whether an error occurred.
	 *
	 * @return boolean
	 */
	public function hasError()
	{
		return ! is_null($this->error());
	}
}
