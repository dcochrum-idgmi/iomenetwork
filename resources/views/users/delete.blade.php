@extends('layouts.'.((Input::get('iframe') !== null) ? 'modal' : 'default'))
@section('content')
@include('layouts.page_header', ['page_header' => trans('users.delete')])
{!! Form::model($user, ['method' => 'DELETE', 'url' => sub_route('users.destroy', ['users' => $user]), 'class' => 'form-horizontal']) !!}
	<div class="form-group">
		<div class="controls">
			{{ trans('users.delete_message') }}<br>
			<button class="btn btn-success btn-sm close_popup">
				<span class="fa fa-ban"></span>
				{{ trans('modal.cancel') }}
			</button>
			<button type="submit" class="btn btn-sm btn-danger">
				<span class="fa fa-trash"></span>
				{{ trans('modal.delete') }}
			</button>
		</div>
	</div>
{!! Form::close() !!}
@stop
