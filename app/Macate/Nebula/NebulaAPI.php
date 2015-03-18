<?php namespace Iome\Macate\Nebula;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use Iome\Office;
use Iome\User;

class NebulaAPI {

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
			'requestId' => get_current_office_slug(),
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
	 * @param array  $filters
	 *
	 * @return array
	 */
	public function getAll($module, $filters = [ ])
	{
		global $currentOffice;

		dd($module, $filters, $currentOffice);

		//		$this->merge_parameters( [ 'module' => 'users', 'action' => 'get-by-field', 'fieldName' => $field, 'field' => $value, 'officeId' => $currentOffice->id ] );
		$this->merge_parameters([
			'module'    => $module,
			'action'    => 'get',
			'fieldName' => $field,
			'field'     => $value,
			'officeId'  => 1
		]);
		$response = $this->get();

		return new User(( $response['success'] ? $response['user'] : [ ] ));
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
		global $currentOffice;

//		$this->merge_parameters( [ 'module' => 'users', 'action' => 'get-by-field', 'fieldName' => $field, 'field' => $value, 'officeId' => $currentOffice->id ] );
		$this->merge_parameters([
			'module'    => 'users',
			'action'    => 'get-by-field',
			'fieldName' => $field,
			'field'     => $value,
			'officeId'  => 1
		]);
		$response = $this->get();

		return new User(( $response['success'] ? $response['user'] : [ ] ));
	}


	/**
	 * Attempt to create a new office
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function officeCreate(array $parameters)
	{
		$this->merge_parameters(array_merge([
			'module' => 'offices',
			'action' => 'create'
		], $parameters));

		$response = $this->post();
		if ( ! $response['success'] )
		{
			return $response;
		}

		return $this->getOffice($response['officeId']);
	}


	/**
	 * Attempt to update an office
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function officeUpdate(array $parameters)
	{
		$officeId = $parameters['officeId'];
		unset( $parameters['officeId'] );
		$this->merge_parameters([
			'module'   => 'offices',
			'action'   => 'edit',
			'office'   => $parameters,
			'officeId' => $officeId
		]);

		return $this->post();
	}


	/**
	 * Retrieve an office matching the given value on the given field.
	 *
	 * @param string $value
	 * @param string $field
	 *
	 * @return array
	 */
	public function getOffice($value, $field = 'officeId')
	{
		$this->merge_parameters([ 'module' => 'offices', 'action' => 'get', $field => $value ]);

		$response = $this->get();

		return new Office(( $response['success'] ? $response['office'] : [ ] ));
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
			$objects = [ 'user' => 'email', 'office' => 'officeId', 'sip' => 'sipAccount' ];
			foreach ($objects as $obj => $id)
			{
				isset( $json[$obj] ) && $json[$obj][$id] && $json[$obj]['exists'] = true;
			}

			return $json;
		}

		return $json;
	}
}