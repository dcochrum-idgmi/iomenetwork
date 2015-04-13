<?php namespace Iome\Macate\Nebula;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Nebula;

class Model extends EloquentModel {

	/**
	 * The Nebula API module for the model.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * Indicates whether attributes are snake cased on arrays.
	 *
	 * @var bool
	 */
	public static $snakeAttributes = false;

	/**
	 * The name of the "created at" column.
	 *
	 * @var string
	 */
	const CREATED_AT = 'dateEntered';

	/**
	 * The name of the "updated at" column.
	 *
	 * @var string
	 */
	const UPDATED_AT = 'dateModified';


	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array $attributes
	 */
	public function __construct(array $attributes = [ ])
	{
		parent::__construct($attributes);

		isset( $attributes['exists'] ) && $this->exists = filter_var($attributes['exists'], FILTER_VALIDATE_BOOLEAN);
	}


	/**
	 * Get the first record matching the attributes.
	 *
	 * @param  array $attributes
	 *
	 * @return Model|null
	 */
	public static function first(array $attributes)
	{
		return Nebula::find(static::getModule(), $attributes);
	}


	/**
	 * Get the first record matching the attributes or create it.
	 *
	 * @param  array $attributes
	 *
	 * @return static
	 */
	public static function firstOrCreate(array $attributes)
	{
		if ( ! is_null($instance = static::first($attributes)) )
		{
			return $instance;
		}

		return static::create(static::getModule(), $attributes);
	}


	/**
	 * Get the first record matching the attributes or instantiate it.
	 *
	 * @param  array $attributes
	 *
	 * @return static
	 */
	public static function firstOrNew(array $attributes)
	{
		if ( ! is_null($instance = static::first($attributes)) )
		{
			return $instance;
		}

		return new static($attributes);
	}


	/**
	 * Get all of the models from the database.
	 *
	 * @param array $parameters
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function all($parameters = [ ])
	{
		static::org_parameter($parameters);

		return Nebula::all(static::getModule(), $parameters);
	}


	/**
	 * Find a model by its primary key.
	 *
	 * @param mixed       $id
	 * @param string|null $by
	 *
	 * @return \Illuminate\Support\Collection|null|static
	 */
	public static function find($id, $by = null)
	{
		return Nebula::find(static::getModule(), $id, $by);
	}


	/**
	 * @param $parameters
	 */
	protected static function org_parameter(&$parameters)
	{
		if ( static::getModule() != 'organizations' && ! isset( $parameters['organizationId'] ) )
		{
			global $currentOrg;
			$parameters['organizationId'] = $currentOrg->organizationId;
		}
	}


	/**
	 * @return mixed
	 */
	protected static function getModule()
	{
		$instance = new static;
		$module   = $instance->getTable();

		return $module;
	}


	/**
	 * Find a model by its primary key or throw an exception.
	 *
	 * @param mixed       $id
	 * @param string|null $by
	 *
	 * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
	 *
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public static function findOrFail($id, $by = null)
	{
		$result = static::find($id, $by);

		if ( is_array($id) )
		{
			if ( count($result) == count(array_unique($id)) )
			{
				return $result;
			}
		}
		elseif ( ! is_null($result) )
		{
			return $result;
		}

		throw (new ModelNotFoundException)->setModel(get_class());
	}


	/**
	 * Find a model by its primary key or throw an exception.
	 *
	 * @param mixed       $id
	 * @param string|null $by
	 *
	 * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
	 *
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public static function findOrNew($id, $by = null)
	{
		$result = static::find($id, $by);

		if ( is_array($id) )
		{
			foreach ($result as &$res)
			{
				if ( is_null($res) )
				{
					$res = new static;
				}
			}

			return $result;
		}
		elseif ( ! is_null($result) )
		{
			return $result;
		}

		return new static;
	}


	/**
	 * Save a new model and return the instance.
	 *
	 * @param  array $attributes
	 *
	 * @return static
	 */
	public static function create(array $attributes)
	{
		$model = new static($attributes);

		if ( ! $model->save() )
		{
			return false;
		}

		return $model;
	}


	/**
	 * Get an array with the values of a given column.
	 *
	 * @param  string $column
	 * @param  string $key
	 *
	 * @return array
	 */
	public static function lists($column, $key = null)
	{
		$models = static::all();

		$values = array_pluck($models, $column);

		if ( ! $key )
		{
			return $values;
		}

		return array_combine(array_pluck($models, $key), $values);
	}


	/**
	 * @param $value
	 * @param $route
	 *
	 * @return Model|null
	 */
	public function bind($value, $route)
	{
		return static::findOrFail($value);
	}


	/**
	 * Reload a fresh model instance from the database.
	 *
	 * @param  array $with
	 *
	 * @return $this
	 */
	public function fresh(array $with = [ ])
	{
		$key = $this->getKeyName();

		return $this->exists ? Nebula::first(static::getModule(), [ $key => $this->getKey() ]) : null;
	}


	/**
	 * Destroy the models for the given IDs.
	 *
	 * @param  array|int $ids
	 *
	 * @return int
	 * @throws Exception
	 */
	public static function destroy($ids)
	{
		// We'll initialize a count here so we will return the total number of deletes
		// for the operation. The developers can then check this number as a boolean
		// type value or get this total count of records deleted for logging, etc.
		$count = 0;

		$ids = is_array($ids) ? $ids : func_get_args();

		$instance = new static;

		// We will actually pull the models from the database table and call delete on
		// each of them individually so that their events get fired properly with a
		// correct set of attributes in case the developers wants to check these.
		$key = $instance->getKeyName();

		foreach ($ids as $id)
		{
			$model    = new static([ $key => $id ]);
			$response = $model->delete();
			if ( $response['success'] )
			{
				$count++;
			}
		}

		return $count;
	}


	/**
	 * Delete the model from the database.
	 * @return bool|null
	 * @throws Exception
	 */
	public function delete()
	{
		if ( is_null($this->primaryKey) )
		{
			throw new Exception("No primary key defined on model.");
		}

		if ( $this->exists )
		{
			if ( $this->fireModelEvent('deleting') === false )
			{
				return false;
			}

			$result = $this->performDeleteOnModel();

			$this->exists = false;

			// Once the model has been deleted, we will fire off the deleted event so that
			// the developers may hook into post-delete operations. We will then return
			// a boolean true as the delete is presumably successful on the database.
			$this->fireModelEvent('deleted', false);

			return $result;
		}
	}


	/**
	 * Perform the actual delete query on this model instance.
	 *
	 * @return array
	 */
	protected function performDeleteOnModel()
	{
		return Nebula::delete(static::getModule(), $this->getKeyForSaveQuery());
	}


	/**
	 * Update the model in the database.
	 *
	 * @param  array $attributes
	 *
	 * @return bool|int
	 */
	public function insert(array $attributes = [ ])
	{
		if ( $this->exists )
		{
			return false;
		}

		return $this->fill($attributes)->save();
	}


	/**
	 * Update the model in the database.
	 *
	 * @param  array $attributes
	 *
	 * @return bool|int
	 */
	public function update(array $attributes = [ ])
	{
		if ( ! $this->exists )
		{
			return false;
		}

		return $this->fill($attributes)->save();
	}


	/**
	 * Save the model to the database.
	 *
	 * @param  array $options
	 *
	 * @return array
	 */
	public function save(array $options = [ ])
	{
		// If the "saving" event returns false we'll bail out of the save and return
		// false, indicating that the save failed. This provides a chance for any
		// listeners to cancel save operations if validations fail or whatever.
		if ( $this->fireModelEvent('saving') === false )
		{
			return false;
		}

		// If the model already exists in the database we can just update our record
		// that is already registered with the API, otherwise, we'll insert a new one.
		if ( $this->exists )
		{
			$response = $this->doUpdate();
		}

		// If the model is brand new, we'll perform an insert request with the API.
		else
		{
			$response = $this->doInsert();
		}

		if ( $response['success'] )
		{
			$this->finishSave($options);
		}

		return $response;
	}


	/**
	 * Perform a model update operation.
	 *
	 * @return array
	 */
	protected function doUpdate()
	{
		$dirty = $this->getDirty();

		if ( count($dirty) > 0 )
		{
			// If the updating event returns false, we will cancel the update operation so
			// developers can hook Validation systems into their models and cancel this
			// operation if the model does not pass validation. Otherwise, we update.
			if ( $this->fireModelEvent('updating') === false )
			{
				return false;
			}

			// Once we have run the update operation, we will fire the "updated" event for
			// this model instance. This will allow developers to hook into these after
			// models are updated, giving them a chance to do any special processing.
			$dirty = $this->getDirty();

			if ( count($dirty) > 0 )
			{
				$response = Nebula::update(static::getModule(), $this->getKeyForSaveQuery(), $dirty);

				$this->fireModelEvent('updated', false);
			}
		}

		return $response;
	}


	/**
	 * Perform a model insert operation.
	 *
	 * @return array
	 */
	protected function doInsert()
	{
		if ( $this->fireModelEvent('creating') === false )
		{
			return false;
		}

		$attributes = $this->attributes;

		// If the table is not incrementing we'll simply insert this attributes as they
		// are, as this attributes arrays must contain an "id" column already placed
		// there by the developer as the manually determined key for these models.
		$response = Nebula::insert(static::getModule(), $attributes);

		// We will go ahead and set the exists property to true, so that it is set when
		// the created event is fired, just in case the developer tries to update it
		// during the event. This will allow them to do so and run an update here.
		$this->exists = true;

		$this->fireModelEvent('created', false);

		return $response;
	}


	/**
	 * Get the attributes that have been changed since last sync.
	 *
	 * @return array
	 */
	public function getDirty()
	{
		$dirty = [ ];

		foreach ($this->attributes as $key => $value)
		{
			if ( ! array_key_exists($key, $this->original) )
			{
				$dirty[$key] = $value;
			}
			elseif ( $value !== $this->original[$key] && ! $this->originalIsNumericallyEquivalent($key) && ! $this->originalIsNullEquivalent($key) )
			{
				$dirty[$key] = $value;
			}
		}

		return $dirty;
	}


	/**
	 * Determine if the new and old values for a given key are null equivalent.
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
	protected function originalIsNullEquivalent($key)
	{
		$current = $this->attributes[$key];
		$current = $current == '' ? null : $current;

		$original = $this->original[$key];
		$original = $original == '' ? null : $original;

		return is_null($current) && is_null($original);
	}


	/**
	 * Get the format for database stored dates.
	 *
	 * @return string
	 */
	protected function getDateFormat()
	{
		return Nebula::getDateFormat();
	}

}
