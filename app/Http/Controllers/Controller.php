<?php namespace Iome\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Flash;
use Input;
use Iome\Macate\Nebula\Nebula;
use Request;
use Response;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	protected function col_as_alias( &$arr ) {
		$arr = array_map( function ( $v ) {
			$v = str_replace( '`', '', $v );
			$split = explode( ' as ', $v );

			return isset( $split[ 1 ] ) ? $split[ 1 ] : $v;
		}, $arr );
	}

	protected function remove_count_cols( &$arr ) {
		$removed = [ ];
		foreach( $arr as $key => $value ) {
			if( stripos( $value, 'count(' ) !== false ) {
				$removed[ 'cols' ][ $key ] = $value;
				$split = explode( '`', $value );
				$removed[ 'tables' ][ $key ] = $split[ 1 ];
				unset( $arr[ $key ] );
			}
		}

		return $removed;
	}

	/**
	 * Send flash message to views upon successful resource creation.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_created( $message = 'Created successfully!' ) {
		Flash::success( $message );
	}

	/**
	 * Send flash message to views upon successful resource update.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_updated( $message = 'Changes saved!' ) {
		Flash::success( $message );
	}

	/**
	 * Send flash message to views upon successful resource removal.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_deleted( $message = 'Deleted successfully!' ) {
		Flash::success( $message );
	}

	public function dataTable( $module, $cols = '*' ) {
		$output = [
			'draw' => ( int )Input::get( 'draw' ),
			'recordsTotal' => 0,
			'recordsFiltered' => 0,
			'data' => [] ];

//		$response = Nebula::getAll( $module, Input::all() );
		$response = [ 'success' => true,
				'offices' => [
					[
						Nebula::getOffice( 1 )->toArray()
					]
				]
			];
		if( ! $response[ 'success' ] )
			return Response::json( $output );

		return Response::json( $output, $response );
	}
}
