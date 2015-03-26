@extends('layouts.'.((Input::get('iframe') !== null) ? 'modal' : 'default'))
@section('content')
@include('layouts.page_header', ['page_header' => trans('ext.'.(isset($ext) ? 'edit' : 'create')).' '.trans('ext.user')])
@if (isset($ext))
{!! Form::model($ext, ['method' => 'PATCH', 'route' => ['ext.update', $ext], 'class' => 'form-horizontal']) !!}
@else
{!! Form::open(['route' => 'users.store', 'class' => 'form-horizontal']) !!}
@endif
	<div class="form-group{!! $errors->has('name') ? ' has-error' : '' !!}">
		{!! Form::label('name', trans('ext.name'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::text('name', null, ['class' =>'form-control', 'placeholder' => trans('ext.name-ph')]) !!}
			{!! $errors->first('name', Form::label('name', ':message')) !!}
		</div>
	</div>
	@if (! isset($ext))
	<div class="form-group{!! $errors->has('email') ? ' has-error' : '' !!}">
		{!! Form::label('email', trans('ext.email'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::email('email', null, ['class' =>'form-control', 'placeholder' => trans('ext.email-ph')]) !!}
			{!! $errors->first('email', Form::label('email', ':message')) !!}
		</div>
	</div>
	@endif
	<div class="form-group{!! $errors->has('password') ? ' has-error' : '' !!}">
		{!! Form::label('password', trans('ext.'.(isset($ext) ? 'new_' : '').'password'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::password('password', ['class' =>'form-control']) !!}
			{!! $errors->first('password', Form::label('password', ':message')) !!}
		</div>
	</div>
	<div class="form-group{!! $errors->has('password_confirmation') ? ' has-error' : '' !!}">
		{!! Form::label('password_confirmation', trans('ext.password_confirmation'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::password('password_confirmation', ['class' =>'form-control']) !!}
			{!! $errors->first('password_confirmation', Form::label('password_confirmation', ':message')) !!}
		</div>
	</div>
	@if (Auth::user()->isAdmin())
	<div class="form-group{!! $errors->has('orgnizationId') ? ' has-error' : '' !!}">
		{!! Form::label('orgnizationId', trans('orgs.org'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::select('orgnizationId', array_merge(['' => ''], $offices), orgnizationId, ['class' =>'form-control', 'data-placeholder' => trans('offices.select')]) !!}
			{!! $errors->first('orgnizationId', Form::label('orgnizationId', ':message')) !!}
		</div>
	</div>
	@endif
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			<div class="checkbox">
				<label>
					{!! Form::hidden('confirmed', '0') !!}
					{!! Form::checkbox('confirmed', '1') !!}
					{!! trans('ext.activate_user') !!}
				</label>
			</div>
		</div>
	</div>
	@if (Auth::user()->isAdmin())
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			<div class="checkbox">
				<label>
					@unless (isset($ext) && Auth::user()->id == $ext->id)
					{!! Form::hidden('admin', '0') !!}
					@endunless
					{!! Form::checkbox('admin', '1', null, ['id' => 'admin', 'disabled' => ((isset($ext) && Auth::user()->id == $ext->id) ? 'disabled' : null)]) !!}
					{!! trans('ext.make_admin') !!}
				</label>
			</div>
		</div>
	</div>
	@endif
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			{!! Form::submit(trans('modal.'.((isset($ext)) ? 'save' : 'create')), ['class' => 'btn btn-primary', 'autocomplete' => 'off']) !!}
			<button class="btn btn-link close_popup">{{{ trans('modal.cancel') }}}</button>
		</div>
	</div>
{!! Form::close() !!}
@stop