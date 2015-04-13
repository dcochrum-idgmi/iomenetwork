<?php namespace Iome\Macate\Nebula\Validation;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->validator->resolver( function( $translator, $data, $rules, $messages = [], $customAttributes = [] ) {
			return new Validator( $translator, $data, $rules, $messages, $customAttributes );
		} );
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
