<?php namespace Iome\Macate\Nebula;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Iome\Organization;
use Iome\User;

class NebulaAPI {

	/**
	 * @var Whether to output debugging messages
	 */
	private $debug = true;

	/**
	 * @var Map of Nebula modules => app models
	 */
	protected $models = [ 'organizations' => 'Organization', 'users' => 'User', 'sipaccounts' => 'Extension' ];

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var string Session ID.
	 */
	protected $sessionId;

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
		$this->default_parameters = [
			'requestId' => get_current_org_slug(),
			'sessionId' => session('nebulaSessionId', null)
		];
		$this->default_options    = [ /*'debug' => true,*/
			'timeout' => 10
		];
		$this->merge_parameters($parameters);
		$this->merge_options($options);
		$this->client = new Client([
			'base_url' => env('NEBULA_ENDPOINT', 'http://52.0.124.75:8080/NebulaWS/webservices/request.jsp'),
			'defaults' => $this->options
		]);

		if ( $this->debug )
		{
			$this->client->getEmitter()->attach(new LogSubscriber);
		}
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
		$parameters['username'] = $parameters['email'];
		unset( $parameters['email'] );

		$this->merge_parameters(array_merge([
			'module'    => 'authentication',
			'action'    => 'login',
			'encrypted' => 'false'
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
	 * Attempt to create a new user
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function userCreate(array $parameters)
	{
		$this->merge_parameters(array_merge([ 'module' => 'users', 'action' => 'create', 'encrypted' => 'false' ],
			$parameters));

		return $this->post();
	}


	/**
	 * Retrieve an array of models matching the given criteria.
	 *
	 * @param string $module
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function getAll($module, $parameters = [ ])
	{
		global $currentOrg;

		$return = [
			'total' . ucfirst($module) => 0,
			'models'                   => [ ]
		];

		$this->merge_parameters(array_merge([
			'module' => $module,
			'action' => 'paginated-list',
			'start'  => 0,
			'end'    => 10,
			//'organizationId' => 1
		], $parameters));
		$response = $this->get();

		if ( ! $response['success'] )
		{
			return $return;
		}

		$return['total' . ucfirst($module)] = $response['total' . ucfirst($module)];

		foreach ($response[$module] as $i => $data)
		{
			$return['models'][$i] = $this->new_model($module, $data);
		}

		return $return;
	}


	/**
	 * Retrieve a user matching the given value on the given field.
	 *
	 * @param string $value
	 * @param string $field
	 *
	 * @return array
	 */
	public function getUser($value, $field = 'email')
	{
		global $currentOrg;

//		$this->merge_parameters( [ 'module' => 'users', 'action' => 'get-by-field', 'fieldName' => $field, 'field' => $value, 'organizationId' => $currentOrg->id ] );
		$this->merge_parameters([
			'module'         => 'users',
			'action'         => 'get-by-field',
			'fieldName'      => $field,
			'field'          => $value,
			'organizationId' => 1
		]);
		$response = $this->get();

		return new User(( $response['success'] ? $response['user'] : [ ] ));
	}


	/**
	 * Attempt to create a new organization
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function organizationCreate(array $data)
	{
		$this->merge_parameters(array_merge([
			'module' => 'organizations',
			'action' => 'create'
		], $data));

		$response = $this->post();
		if ( ! $response['success'] )
		{
			return $response;
		}

		return $this->getOrganization($response['organizationId']);
	}


	/**
	 * Attempt to update an organization
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function organizationUpdate(array $data)
	{
		$organizationId = $data['organizationId'];
		unset( $data['organizationId'] );
		$this->merge_parameters([
			'module'         => 'organizations',
			'action'         => 'edit',
			'organization'   => $data,
			'organizationId' => $organizationId
		]);

		return $this->post();
	}


	/**
	 * Retrieve an organization matching the given value on the given field.
	 *
	 * @param string $value
	 * @param string $field
	 *
	 * @return array
	 */
	public function getOrganization($value, $field = 'organizationId')
	{
		$this->merge_parameters([
			'module' => 'organizations',
			'action' => 'get' . ( $field == 'slug' ? '-by-slug' : '' ),
			$field   => $value . ( strpos($value, '.') !== false ? '' : '.' . config('app.domain') )
		]);

		$response = $this->get();

		return new Organization(( $response['success'] ? $response['organization'] : [ ] ));
	}


	/**
	 * Retrieve an organization matching the given slug.
	 *
	 * @param string $value
	 *
	 * @return array
	 */
	public function getOrganizationBySlug($value)
	{
		$this->merge_parameters([
			'module' => 'organizations',
			'action' => 'get-by-slug',
			'slug'   => $value . '.' . config('app.domain')
		]);

		$response = $this->get();

		return new Organization(( $response['success'] ? $response['organization'] : [ ] ));
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
	 * @param $module
	 * @param $data
	 *
	 * @return Model
	 */
	protected function new_model($module, $data)
	{
		$model = $this->models[$module];

		return new $model($data);
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
			abort('500', json_encode([ $this->build_request(), 'sessionId' => session('nebulaSessionId') ], true));
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
		$this->parameters = $this->default_parameters;
		$this->options    = $this->default_options;
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
			$objects = [ 'user' => 'email', 'organization' => 'organizationId', 'sip' => 'sipAccount' ];
			foreach ($objects as $obj => $id)
			{
				isset( $json[$obj] ) && $json[$obj][$id] && $json[$obj]['exists'] = true;
			}

			return $json;
		}

		return $json;
	}
}