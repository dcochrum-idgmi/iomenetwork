<?php namespace Iome\Http\Controllers;

use Auth;
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


	protected function col_as_alias(&$arr)
	{
		$arr = array_map(function ($v)
		{
			$v     = str_replace('`', '', $v);
			$split = explode(' as ', $v);

			return isset( $split[1] ) ? $split[1] : $v;
		}, $arr);
	}


	protected function remove_count_cols(&$arr)
	{
		$removed = [ ];
		foreach ($arr as $key => $value)
		{
			if ( stripos($value, 'count(') !== false )
			{
				$removed['cols'][$key]   = $value;
				$split                   = explode('`', $value);
				$removed['tables'][$key] = $split[1];
				unset( $arr[$key] );
			}
		}

		return $removed;
	}


	/**
	 * Send flash message to views upon successful resource creation.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_created($message = 'Created successfully!')
	{
		Flash::success($message);
	}


	/**
	 * Send flash message to views upon successful resource update.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_updated($message = 'Changes saved!')
	{
		Flash::success($message);
	}


	/**
	 * Send flash message to views upon successful resource removal.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_deleted($message = 'Deleted successfully!')
	{
		Flash::success($message);
	}


	public function dataTable($module, $cols = '*')
	{
		$data         = [ ];
		$cols         = array_pluck(Input::get('columns'), 'data');
		$output       = [
			'draw'            => ( int ) Input::get('draw'),
			'recordsTotal'    => 0,
			'recordsFiltered' => 0,
			'data'            => [ ]
		];
		$resources    = [
			'organizations' => 'orgs',
			'users'         => 'users',
			'sips'          => 'sips'
		];
		$route_method = ( ( $module == 'organizations' ) ? 'admin' : 'sub' ) . '_route';

		$response = Nebula::getAll($module,
			[ 'start' => Input::get('start'), 'end' => ( Input::get('length') + Input::get('start') ) ]);

		foreach ($response['models'] as $model)
		{

			//if(isset($model['slug']) && strpos($model['slug'], config('app.domain')) !== false)
			//	Nebula::organizationUpdate(['organizationId' => $model['organizationId'], 'slug' => ($model['organizationId'] == 1 ? 'admin' : str_replace('.'.config('app.domain'), '', $model['slug']))]);
			$model_data            = $model->toArray();
			$model_data['actions'] = '<a href="' . $route_method($resources[$module] . '.edit',
					[ $resources[$module] => $model ]) . '" class="btn btn-primary btn-sm iframe" title="' . trans('modal.edit') . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><span class="sr-only">' . trans('modal.edit') . '</span></a>';
			( $model->organizationId != 16 ) && $model_data['actions'] .= '<a href="' . $route_method($resources[$module] . '.delete',
					[ $resources[$module] => $model ]) . '" class="btn btn-sm btn-danger iframe" title="' . trans('modal.delete') . '"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span><span class="sr-only">' . trans('modal.delete') . '</span></a>';
			//$model_data = array_only($model_data, $cols);

			$data[] = $model_data;
		}

		$output['recordsTotal']    = $response['total' . ucfirst($module)];
		$output['recordsFiltered'] = count($data);
		$output['data']            = $data;

		return Response::json($output);
	}
}
