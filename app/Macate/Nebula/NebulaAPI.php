<?php namespace Iome\Macate\Nebula;

use GuzzleHttp\Client;
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
	 * @var array
	 */
	private $parameters;

	/**
	 * @param array $parameters
	 * @param array $options
	 */
	public function __construct( array $parameters = [ ], array $options = [ ] ) {
		$this->parameters = array_merge( [ 'requestId' => get_current_office_slug(), 'sessionId' => session( 'nebulaSessionId', null ) ],
			$parameters );
		$options = array_merge( [ /*'debug' => true,*/
			'timeout' => 10 ],
			$options );
		$this->client = new Client( [
			'base_url' => env( 'NEBULA_ENDPOINT', 'http://52.0.124.75:8080/NebulaWS/webservices/request.jsp' ),
			'defaults' => $options
		] );
	}

	/**
	 * Attempt to auth
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function login( array $parameters ) {
		$parameters[ 'username' ] = $parameters[ 'email' ];
		unset( $parameters[ 'email' ] );

		$this->merge_parameters( array_merge( [ 'module' => 'authentication', 'action' => 'login', 'encrypted' => 'false' ],
			$parameters ) );

		$result = $this->post();

		$result[ 'success' ] && session( [ 'nebulaSessionId' => $result[ 'sessionId' ] ] );

		return $result;
	}

	/**
	 * Ensure session is still valid
	 *
	 * @return array
	 */
	public function checkSession() {
		if( $this->parameters[ 'sessionId' ] == null )
			return false;

		$this->merge_parameters( [ 'module' => 'authentication', 'action' => 'check-session' ] );

		$response = $this->get();

		return $response[ 'success' ];
	}

	/**
	 * Attempt to auth
	 *
	 * @return array
	 */
	public function logout() {
		if( $this->parameters[ 'sessionId' ] == null )
			return true;

		$this->merge_parameters( [ 'module' => 'authentication', 'action' => 'logout' ] );

		$response = $this->post();

		return $response[ 'success' ];
	}

	/**
	 * Attempt to create a new user
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function userCreate( array $parameters ) {
		$this->merge_parameters( array_merge( [ 'module' => 'users', 'action' => 'create', 'encrypted' => 'false' ],
			$parameters ) );

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
	public function getAll( $module, $filters = [] ) {
		global $currentOffice;

		dd( $module, $filters, $currentOffice );

		//		$this->merge_parameters( [ 'module' => 'users', 'action' => 'get-by-field', 'fieldName' => $field, 'field' => $value, 'officeId' => $currentOffice->id ] );
		$this->merge_parameters( [ 'module' => $module, 'action' => 'get', 'fieldName' => $field, 'field' => $value, 'officeId' => 1 ] );
		$response = $this->get();

		return new User( ( $response[ 'success' ] ? $response[ 'user' ] : [] ) );
	}

	/**
	 * Retrieve a user matching the given value on the given field.
	 *
	 * @param string $value
	 * @param string $field
	 *
	 * @return array
	 */
	public function getUser( $value, $field = 'email' ) {
		global $currentOffice;

//		$this->merge_parameters( [ 'module' => 'users', 'action' => 'get-by-field', 'fieldName' => $field, 'field' => $value, 'officeId' => $currentOffice->id ] );
		$this->merge_parameters( [ 'module' => 'users', 'action' => 'get-by-field', 'fieldName' => $field, 'field' => $value, 'officeId' => 1 ] );
		$response = $this->get();

		return new User( ( $response[ 'success' ] ? $response[ 'user' ] : [] ) );
	}

	/**
	 * Attempt to create a new office
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function officeCreate( array $parameters ) {
		$this->merge_parameters( array_merge( [ 'module' => 'offices', 'action' => 'create' ],
			$parameters ) );

		return $this->post();
	}

	/**
	 * Attempt to update an office
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function officeUpdate( array $parameters ) {
		$this->merge_parameters( [ 'module' => 'offices', 'action' => 'edit', 'office' => $parameters ] );

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
	public function getOffice( $value, $field = 'email' ) {
		$this->merge_parameters( [ 'module' => 'offices', 'action' => 'get', 'officeId' => $value ] );
		die( json_encode( $this->parameters ) );
		$response = $this->get();

		return new Office( ( $response[ 'success' ] ? $response[ 'office' ] : [] ) );
	}

	/**
	 * @param array $parameters
	 */
	protected function merge_parameters( array $parameters ) {
		$this->parameters = array_merge( $this->parameters, $parameters );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function get() {
		return $this->request( 'get' );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function post() {
		return $this->request( 'post' );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function put() {
		return $this->request( 'put' );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function patch() {
		return $this->request( 'patch' );
	}

	/**
	 * @param string $method
	 *
	 * @return \Guzzle\Http\Message\Response
	 */
	private function request( $method ) {
		try {
			if( strtolower( $method ) == 'get' )
				$response = $this->client->get( '', [ 'query' => $this->build_request() ] )->json();
			else
				$response = $this->client->{$method}( '', [ 'body' => $this->build_request() ] )->json();

			array_walk_recursive( $response, function ( &$item )
			{
				if( $item == 'true' )
					$item = true;
				else if( $item == 'false' )
					$item = false;
				else if( strtolower( $item ) == 'null' || $item == '' )
					$item = null;
			} );

			if( $response[ 'success' ] ) {
				$objects = [ 'user' => 'email', 'office' => 'officeId', 'sip' => 'sipAccount' ];
				foreach( $objects as $obj => $id ) {
					isset( $response[ $obj ] ) && $response[ $obj ][ $id ] && $response[ $obj ][ 'exists' ] = true;
//					echo '<pre>' . var_export( $response[ $obj ], true ) . '</pre>';
				}
			}

			return $response;
		} catch( RequestException $e ) {
//			if( $e->hasResponse() )
			abort( '500', json_encode( [ $this->build_request(), 'sessionId' => session( 'nebulaSessionId' ) ], true ) );
//			else
			abort( 503 );
		} catch( Exception $e ) {

		}
	}

	/**
	 * @return array
	 */
	private function build_request() {
		$params = $this->parameters;
		array_walk_recursive( $params, function ( &$item )
		{
			$item = ( string )$item;
		} );
		$params = json_encode( $params );

		return [ 'request' => $params ];
	}
}