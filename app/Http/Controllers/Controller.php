<?php namespace Iome\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Flash;
use Input;
use Iome\Macate\Nebula\Model;
use Nebula;
use Organization;
use Response;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;


	/**
	 * @return array
	 */
	public function get_org_parameter()
	{
		global $currentOrg;

		$parameters = [ ];
		if ( $currentOrg->isMaster() && Auth::user()->isMasterAdmin() )
		{
			$parameters['organizationId'] = '';
		}

		return $parameters;
	}


	/**
	 * @param $arr
	 */
	protected function col_as_alias(&$arr)
	{
		$arr = array_map(function ($v)
		{
			$v     = str_replace('`', '', $v);
			$split = explode(' as ', $v);

			return isset( $split[1] ) ? $split[1] : $v;
		}, $arr);
	}


	/**
	 * @param $arr
	 *
	 * @return array
	 */
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
	 * Send flash message to views upon successful resource creation.
	 *
	 * @param  string $message Message to display.
	 */
	protected function flash_error($message = 'An error has occurred!')
	{
		Flash::error($message);
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


	/**
	 * @return array
	 */
	protected function list_orgs()
	{
		if ( ! Auth::user()->isMasterAdmin() )
		{
			return [ ];
		}

		$orgs  = Organization::all([ 'fields' => 'organizationName', 'orders' => 'ASC' ])->toArray();
		$names = array_pluck($orgs, 'organizationName');
		$slugs = array_pluck($orgs, 'slug');
		$ids   = array_pluck($orgs, 'organizationId');

		$display = [ ];
		foreach ($names as $i => $name)
		{
			$display[$i] = $name . ' (' . $slugs[$i] . ')';
		}

		return array_combine($ids, $display);
	}


	/**
	 * @param string $countryId
	 *
	 * @return array
	 */
	public function list_states($countryId = 'US')
	{
		return Nebula::getStates($countryId);
	}


	/**
	 * @return array
	 */
	public function list_countries()
	{
		return Nebula::getCountries();
	}


	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function dataTable()
	{
		$controller   = class_basename(get_class($this));
		$model        = str_replace('Controller', '', $controller);
		$stamp_fields = [ $model::CREATED_AT, $model::UPDATED_AT ];
		$now          = new Carbon;
		$resource     = $this->route_resource;
		$data         = [ ];
		$cols         = array_pluck(Input::get('columns'), 'data');
		$output       = [
			'draw'            => ( int ) Input::get('draw'),
			'recordsTotal'    => 0,
			'recordsFiltered' => 0,
			'data'            => [ ]
		];

		$fields = $orders = [ ];
		if ( Input::has('order.0.column') && Input::has('order.0.dir') )
		{
			$orderby = array_pluck(Input::get('order'), 'column');
			$orders  = array_pluck(Input::get('order'), 'dir');
			if ( count($orderby) == count($orders) )
			{
				foreach ($orderby as $index)
				{
					$field    = $cols[$index];
					$fields[] = $field;

					//  Reverse the direction of the created & updated columns since we're displaying them as time diffs
					if ( in_array($field, $stamp_fields) )
					{
						$i          = key($fields);
						$orders[$i] = strtoupper($orders[$i]) == 'DESC' ? 'ASC' : 'DESC';
					}
				}
			}
		}

		$search_cols = array_where($cols, function ($key, $value)
		{
			return filter_var(Input::get('columns.' . $key . '.searchable', false), FILTER_VALIDATE_BOOLEAN);
		});

		$parameters = array_merge([
			'start'       => Input::get('start', 0),
			'end'         => ( Input::get('length', 10) + Input::get('start', 0) ),
			'fields'      => implode(':', $fields),
			'orders'      => strtoupper(implode(':', $orders)),
			'search'      => Input::get('search.value', ''),
			'search_cols' => implode(':', $search_cols),
		], $this->get_org_parameter());
		$collection = $model::all($parameters);

		if ( $collection->hasError() )
		{
			$output['error'] = $collection->error();

			return $output;
		}

		foreach ($collection as $model)
		{
			$model_data                      = $model->toArray();
			$model_data['actions']['edit']   = '<a href="' . sub_route($resource . '.edit',
					[ $resource => $model ]) . '" class="btn btn-primary btn-sm iframe" title="' . trans('modal.edit') . ' ' . $model->name . '"><span class="fa fa-pencil" aria-hidden="true"></span><span class="sr-only">' . trans('modal.edit') . '</span></a>';
			$model_data['actions']['delete'] = '<a href="' . sub_route($resource . '.delete',
					[ $resource => $model ]) . '" class="btn btn-sm btn-danger iframe" title="' . trans('modal.delete') . ' ' . $model->name . '"><span class="fa fa-trash" aria-hidden="true"></span><span class="sr-only">' . trans('modal.delete') . '</span></a>';

			foreach ($stamp_fields as $field)
			{
				if ( isset( $model_data[$field] ) )
				{
					$date = Carbon::parse($model->{$field});
					// Creating/editing in the future is still too new a concept for your average user to grasp.
					// Let's try not to freak them out too much and just pretend the server times are the same.
					if ( $date->gt($now) )
					{
						$date = $now;
					}
					$model_data[$field] = '<abbr class="time-diff" title="' . $date->toRfc2822String() . '">' . $date->diffForHumans() . '</abbr>';
				}
			}

			if ( method_exists($this, 'filterModelData') )
			{
				$model_data = $this->filterModelData($model, $model_data);
			}

			$model_data['actions'] = implode('', $model_data['actions']);

			$data[] = array_only($model_data, $cols);
		}

		$output['recordsTotal']    = $collection->total();
		$output['recordsFiltered'] = count($data);
		$output['data']            = $data;

		return $output;
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param FormRequest $request
	 * @param Model       $model
	 * @param string      $success_redirect
	 * @param string      $error_redirect
	 * @param array       $data
	 *
	 * @return Response
	 */
	protected function do_create(
		FormRequest $request,
		Model $model,
		$success_redirect,
		$error_redirect,
		$data = [ ]
	) {
		$data     = $data ?: $request->all();
		$response = $model->fill($data)->save();

		return $this->process_crud_response('create', $response, $request, $model, $success_redirect, $error_redirect);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param FormRequest $request
	 * @param Model       $model
	 * @param string      $success_redirect
	 * @param string      $error_redirect
	 * @param array       $data
	 *
	 * @return Response
	 */
	protected function do_update(
		FormRequest $request,
		Model $model,
		$success_redirect,
		$error_redirect,
		$data = [ ]
	) {
		$data     = $data ?: $request->all();
		$response = $model->update($data);

		return $this->process_crud_response('update', $response, $request, $model, $success_redirect, $error_redirect);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param FormRequest $request
	 * @param Model       $model
	 * @param string      $success_redirect
	 * @param string      $error_redirect
	 *
	 * @return Response
	 */
	protected function do_destroy(
		FormRequest $request,
		Model $model,
		$success_redirect,
		$error_redirect
	) {
		$response = $model->delete();

		return $this->process_crud_response('delete', $response, $request, $model, $success_redirect, $error_redirect);
	}


	/**
	 * @param string      $action
	 * @param array       $response
	 * @param FormRequest $request
	 * @param Model       $model
	 * @param string      $success_redirect
	 * @param string      $error_redirect
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Response
	 */
	protected function process_crud_response(
		$action,
		$response,
		FormRequest $request,
		Model $model,
		$success_redirect,
		$error_redirect
	) {
		if ( $response['success'] )
		{
			if ( $request->wantsJson() )
			{
				if ( $action == 'delete' )
				{
					return response(null, 204);
				}

				return response($model, 200);
			}

			$this->{'flash_' . $action . 'd'}();

			return redirect($success_redirect);
		}

		if ( $request->wantsJson() )
		{
			return response([ 'status' => 'error', 'general' => $response['errorMsg'] ], $response['error']);
		}

		$this->flash_error($response['errorMsg']);

		return redirect($error_redirect)->withInput($request->except($model->getHidden()));
	}
}
