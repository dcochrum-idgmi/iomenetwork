<?php

return [

	/**
	 * Display Guzzle HTTP request log.
	 */
	'debug'        => false, //env('APP_DEBUG'),
	/**
	 * Format of Guzzle HTTP debug output.
	 */
	'debug_format' => "<p>{resource}<br>{req_body}<br>{res_body}</p>",
	/**
	 * Nebula server endpoint.
	 */
	'endpoint'     => env('NEBULA_ENDPOINT'),
	/**
	 * Timestamp format in Nebula response.
	 */
	'date_format'  => 'Y-m-d H:i:s.u',
	/**
	 * Format of Guzzle HTTP request log.
	 */
	'log_format' => "\r\n{resource}\r\n{req_body}\r\n{res_body}",
	/**
	 * Map of Nebula modules => app models.
	 */
	'models'       => [
		'organizations' => 'Organization',
		'users'         => 'User',
		'sipusers'      => 'Extension'
	],
	/**
	 * Number of seconds before timing out requests to Nebula server.
	 */
	'timeout'      => 10,

];
