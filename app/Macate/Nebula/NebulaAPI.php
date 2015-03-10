<?php namespace Iome\Macate\Nebula;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class NebulaAPI
{

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
	public function __construct( array $parameters = [ ], array $options = [ ] )
	{
		$this->parameters = array_merge( [ 'requestId' => get_current_org_slug(), 'sessionId' => session( 'nebulaSessionId', null ) ],
			$parameters );
		$options = array_merge( [ /*'debug' => true,*/ 'timeout' => 10 ],
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
	public function login( array $parameters )
	{
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
	public function checkSession()
	{
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
	public function logout()
	{
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
	public function userCreate( array $parameters )
	{
		$parameters[ 'officeId' ] = $parameters[ 'organizationId' ];
		unset( $parameters[ 'organizationId' ] );

		$this->merge_parameters( array_merge( [ 'module' => 'users', 'action' => 'create', 'encrypted' => 'false' ],
			$parameters ) );

		return $this->post();
	}

	/**
	 * Attempt to create a new organization
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function organizationCreate( array $parameters )
	{
		$this->merge_parameters( array_merge( [ 'module' => 'offices', 'action' => 'create' ],
			$parameters ) );

		return $this->post();
	}

	/**
	 * @param array $parameters
	 */
	protected function merge_parameters( array $parameters )
	{
		$this->parameters = array_merge( $this->parameters, $parameters );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function get()
	{
		return $this->request( 'get' );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function post()
	{
		return $this->request( 'post' );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function put()
	{
		return $this->request( 'put' );
	}

	/**
	 * @return \Guzzle\Http\Message\Response
	 */
	private function patch()
	{
		return $this->request( 'patch' );
	}

	/**
	 * @param string $method
	 *
	 * @return \Guzzle\Http\Message\Response
	 */
	private function request( $method )
	{
		try {
			if( strtolower( $method ) == 'get' )
				$response = $this->client->get( '', [ 'query' => $this->build_request() ] )->json();
			else
				$response = $this->client->{$method}( '', [ 'body' => $this->build_request() ] )->json();

			$response[ 'success' ] = filter_var( $response[ 'success' ], FILTER_VALIDATE_BOOLEAN );

			return $response;
		} catch( RequestException $e ) {
			if( $e->hasResponse() )
				abort( '500', var_export( [ $this->build_request(), 'sessionId' => session( 'nebulaSessionId' ) ], true ) );
			else
				abort( 503 );
		} catch( Exception $e ) {

		}
	}

	/**
	 * @return array
	 */
	private function build_request()
	{
		return [ 'request' => json_encode( $this->parameters ) ];
	}
}