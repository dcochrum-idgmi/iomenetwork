<?php namespace Iome\Macate\Nebula;

use Aws\CloudFront\Exception\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Hash;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Request;

class API {

	/**
	 * @var Whether to output debugging messages
	 */
	private $debug;

	/**
	 * @var Map of Nebula modules => app models
	 */
	protected $models;

	/**
	 * @var string Nebula response timestamps date format.
	 */
	protected $date_format;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var string Session ID.
	 */
	protected $sessionId;

	/**
	 * @var boolean Whether to halt when API request returns success as false.
	 */
	protected $abort_on_error;

	/**
	 * @var array Array of all parameters for the request.
	 */
	private $parameters;

	/**
	 * @var array Array of the default parameters to be reused in all requests.
	 */
	private $default_parameters;

	/**
	 * @var array Array of all options for the request.
	 */
	private $options;

	/**
	 * @var array Array of the default options to be reused in all requests.
	 */
	private $default_options;


	/**
	 * @param array $parameters
	 * @param array $options
	 */
	public function __construct(array $parameters = [ ], array $options = [ ])
	{
		$this->debug       = config('nebula.debug');
		$this->date_format = config('nebula.date_format');
		$this->models      = config('nebula.models');

		$this->default_parameters = [
			'requestId' => Request::url(),
			'sessionId' => session('nebulaSessionId', null)
		];
		$this->default_options    = [
			/*'debug'        => $this->debug,*/
			'timeout' => config('nebula.timeout'),
		];

		$this->abort_on_error = array_pull($options, 'abort_on_error', false);

		$this->merge_parameters($parameters);
		$this->merge_options($options);
		$this->client = new Client([
			'base_url' => config('nebula.endpoint'),
			'defaults' => $this->options
		]);

		$log = new Logger('nebula');
		$log->pushHandler(new StreamHandler(storage_path() . '/logs/nebula.log'));
		$this->client->getEmitter()->attach(new LogSubscriber($log, config('nebula.log_format')));

		if ( $this->debug )
		{
			$this->client->getEmitter()->attach(new LogSubscriber(null, config('nebula.debug_format')));
		}
	}


	/**
	 * @return string
	 */
	public function getDateFormat()
	{
		return $this->date_format;
	}


	/**
	 * Attempt to auth
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function login(array $parameters)
	{
		$this->merge_parameters(array_merge([
			'module'    => 'authentication',
			'action'    => 'login',
			'encrypted' => 'false',
		], $parameters));

		$result = $this->post();

		$result['success'] && session([ 'nebulaSessionId' => $result['sessionId'] ]);

		return $result;
	}


	/**
	 * Ensure session is still valid
	 *
	 * @return array
	 */
	public function checkSession()
	{
		if ( $this->parameters['sessionId'] == null )
		{
			return false;
		}

		$this->merge_parameters([ 'module' => 'authentication', 'action' => 'check-session' ]);

		$response = $this->get();

		return $response['success'];
	}


	/**
	 * Attempt to auth
	 *
	 * @return array
	 */
	public function logout()
	{
		if ( $this->parameters['sessionId'] == null )
		{
			return true;
		}

		$this->merge_parameters([ 'module' => 'authentication', 'action' => 'logout' ]);

		$response = $this->post();

		return $response['success'];
	}


	/**
	 * @param string $module
	 * @param array  $parameters
	 *
	 * @return Collection
	 */
	public function all($module, array $parameters = [ ])
	{
		$currentOrg = $this->getCurrentOrg();

		$return = [ ];
		$total  = 0;

		$parameters = array_merge([
			'module' => $module,
			'action' => 'paginated-list',
			'start'  => 0,
			'end'    => 999,
		], $parameters);

		$this->merge_parameters($parameters);
		$response = $this->get();

		if ( $response['success'] )
		{
			$total = $response['total'];
			foreach ($response[$module] as $i => $data)
			{
				$return[$i] = $this->populateModel($module, $data, true);
			}
		}

		return new Collection($return, $total, array_get($response, 'errorMsg', null));
	}


	/**
	 * Retrieve a user matching the given value on the given field.
	 *
	 * @param string      $module
	 * @param mixed       $id
	 * @param string|null $by
	 *
	 * @return array
	 */
	public function find($module, $id, $by = null)
	{
		$by = $by ?: $this->getModelKeyName($module);

		$this->merge_parameters([
			'module'    => $module,
			'action'    => 'get-by-field',
			'fieldName' => $by,
			'field'     => $id,
		]);

		$response = $this->get();

		return ( $response['success'] ? $this->populateModel($module, $response[str_singular($module)], true) : null );
	}


	/**
	 * @param $module
	 * @param $data
	 *
	 * @return \Guzzle\Http\Message\Response
	 */
	public function insert($module, $data = [ ])
	{
		return $this->operation('create', $module, null, $data);
	}


	/**
	 * Attempt to update a model.
	 *
	 * @param string $module
	 * @param mixed  $id
	 * @param array  $data
	 *
	 * @return array
	 */
	public function update($module, $id, $data = [ ])
	{
		return $this->operation('edit', $module, $id, $data);
	}


	/**
	 * Attempt to delete a model.
	 *
	 * @param string $module
	 * @param mixed  $id
	 *
	 * @return array
	 */
	public function delete($module, $id)
	{
		return $this->operation('delete', $module, $id);
	}


	/**
	 * Attempt to add/edit/delete a model.
	 *
	 * @param string $action
	 * @param string $module
	 * @param null   $id
	 * @param array  $data
	 *
	 * @return array
	 */
	protected function operation($action, $module, $id = null, $data = [ ])
	{
		$parameters = [
			'module' => $module,
			'action' => $action,
		];

		if ( $action != 'create' )
		{
			$parameters[$this->getModelKeyName($module)] = $id;
		}
		if ( $action != 'delete' )
		{
			if ( isset( $data['password'] ) )
			{
				$data['encrypted'] = 'false';
			}
			$parameters[str_singular($module)] = $data;
		}

		$this->merge_parameters($parameters);

		return $this->post();
	}


	/**
	 * Retrieve an array of countries in name=>OSI code format.
	 *
	 * @return array
	 */
	public function getCountries()
	{
		$this->merge_parameters([ 'module' => 'utils', 'action' => 'get-country-list' ]);
		$response = $this->get();

		return ( $response['success'] ? $this->convert_name_val_pairs($response['countries']) : [ ] );
	}


	/**
	 * Retrieve an array of states in name=>OSI code format.
	 *
	 * @param string $countryId
	 *
	 * @return array
	 */
	public function getStates($countryId = 'US')
	{
		$this->merge_parameters([ 'module' => 'utils', 'action' => 'get-state-list', 'countryId' => $countryId ]);
		$response = $this->get();

		return ( $response['success'] ? $this->convert_name_val_pairs($response['states']) : [ ] );
	}


	/**
	 * Retrieve an array of object totals.
	 *
	 * @return array
	 */
	public function getTotals()
	{
		$this->merge_parameters([ 'module' => 'utils', 'action' => 'get-totals' ]);

		return $this->get();
	}


	/**
	 * Convert numerically indexed array of names and values to value => key for use in select elements.
	 *
	 * @param array $arr
	 *
	 * @return mixed
	 */
	protected function convert_name_val_pairs($arr)
	{
		foreach ($arr as $i => $pair)
		{
			$arr[$pair['value']] = $pair['name'];
			unset( $arr[$i] );
		}

		asort($arr);

		return $arr;
	}


	/**
	 * @param array $parameters
	 */
	protected function merge_parameters(array $parameters)
	{
		empty( $this->parameters ) && $this->parameters = $this->default_parameters;
		$this->parameters = array_merge($this->parameters, $parameters);
	}


	/**
	 * @param array $options
	 */
	protected function merge_options(array $options)
	{
		empty( $this->options ) && $this->options = $this->default_options;
		$this->options = array_merge($this->options, $options);
	}


	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function get()
	{
		return $this->request('get');
	}


	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function post()
	{
		return $this->request('post');
	}


	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function put()
	{
		return $this->request('put');
	}


	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function patch()
	{
		return $this->request('patch');
	}


	/**
	 * @param string $method
	 *
	 * @return \Guzzle\Http\Message\Response
	 */
	private function request($method)
	{
		try
		{
			if ( strtolower($method) == 'get' )
			{
				$response = $this->client->get('', [ 'query' => $this->build_request() ]);
			}
			else
			{
				$response = $this->client->{$method}('', [ 'body' => $this->build_request() ]);
			}

			$json = $response->json();

			$json = $this->process_response($json);

			$this->reset_options_params();

			return $json;
		}
		catch (RequestException $e)
		{
			$this->reset_options_params();

//			if( $e->hasResponse() )
			abort('500', json_encode([
				$this->build_request(),
				'sessionId' => session('nebulaSessionId'),
				'message'   => $e->getMessage()
			], true));
//			else
			abort(503);
		}
		catch (ParseException $e)
		{
			$body = preg_replace([ '/(?<="dateEntered":)(.[^,]*)/', '/(?<=\"dateModified\":)(.[^,]*)/' ], "\"$1\"",
				trim($response->getBody()));
			$json = json_decode($body, true);
			$this->reset_options_params();

			return $this->process_response($json);
		}
		catch (Exception $e)
		{
			$this->reset_options_params();

		}
	}


	/**
	 * @return array
	 */
	private function build_request()
	{
		$params = $this->parameters;
		array_walk_recursive($params, function (&$item)
		{
			$item = ( string ) $item;
		});
		$params = json_encode($params);

		return [ 'request' => $params ];
	}


	/**
	 * Resets options and parameters to default to prepare for another request.
	 */
	protected function reset_options_params()
	{
		$this->abort_on_error = false;
		$this->parameters     = $this->default_parameters;
		$this->options        = $this->default_options;
	}


	/**
	 * @param $json
	 *
	 * @return mixed
	 */
	private function process_response($json)
	{
		array_walk_recursive($json, function (&$item)
		{
			if ( $item == 'true' )
			{
				$item = true;
			}
			else
			{
				if ( $item == 'false' )
				{
					$item = false;
				}
				else
				{
					if ( strtolower($item) == 'null' || $item == '' )
					{
						$item = null;
					}
				}
			}
		});

		if ( $json['success'] )
		{
			//$objects = [ 'user' => 'email', 'organization' => 'organizationId', 'sip' => 'sipAccount' ];
			//foreach ($objects as $obj => $id)
			//{
			//	isset( $json[$obj] ) && $json[$obj][$id] && $json[$obj]['exists'] = true;
			//}

			return $json;
		}
		else
		{
			if ( $this->abort_on_error )
			{
				abort($json['error'], trim($json['errorMsg']) . "\n\n" . json_encode($this->parameters));
			}
		}

		return $json;
	}


	public function getModelName($module)
	{
		return $this->models[$module];
	}


	public function getModelKeyName($module)
	{
		$model = $this->newModel($module);

		return $model->getKeyName();
	}


	/**
	 * @param string $module
	 * @param array  $data
	 * @param bool   $sync_original
	 *
	 * @return EloquentModel
	 */
	public function newModel($module, array $data = [ ], $sync_original = false)
	{
		$class = $this->getModelName($module);

		$model = new $class($data);

		if ( isset( $model->{$class::CREATED_AT} ) )
		{
			$model->exists = true;
		}

		if ( $sync_original )
		{
			$model->syncOriginal();
		}

		return $model;
	}


	/**
	 * @param string $module
	 * @param array  $data
	 * @param bool   $sync_original
	 *
	 * @return EloquentModel
	 */
	public function populateModel($module, array $data = [ ], $sync_original = false)
	{
		$model = $this->newModel($module, $data, $sync_original);

		return $model->exists ? $model : null;
	}


	public function getCurrentOrg()
	{
		global $currentOrg;

		return $currentOrg ?: $this->newModel('organizations',
			[ 'slug' => str_replace('.' . config('app.domain'), '', Request::server('SERVER_NAME')) ]);
	}


	/**
	 * @param string $username
	 * @param string $password
	 *
	 * @return Hash
	 */
	public function hash($username, $password)
	{
		return hash('sha256', $username . $password);
	}
}