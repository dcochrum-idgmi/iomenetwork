@extends('layouts.'.((Input::get('iframe') !== null) ? 'modal' : 'default'))
@section('content')
@include('layouts.page_header', ['page_header' => trans('site.'.(isset($ext) ? 'edit' : 'create')).' '.trans('ext.extension')])
@if (isset($ext))
{!! Form::model($ext, ['method' => 'PATCH', 'route' => ['exts.update', $currentOrg, $ext], 'class' => 'form-horizontal']) !!}
@else
{!! Form::open(['route' => [ 'exts.store', $currentOrg ], 'class' => 'form-horizontal']) !!}
@endif
	<div class="form-group{!! $errors->has('extension') ? ' has-error' : '' !!}">
		{!! Form::label('extension', trans('ext.extension'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::text('extension', null, [ 'class' =>'form-control', 'placeholder' => trans('ext.extension-ph'), 'required' => '' ] ) !!}
			{!! $errors->first('extension', Form::label('extension', ':message')) !!}
		</div>
	</div>
	@if ( ! isset( $ext ) )
	<div class="form-group{!! $errors->has('secret') ? ' has-error' : '' !!}">
		{!! Form::label('secret', trans('user.password'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::password('secret', [ 'class' =>'form-control', 'required' => '' ] ) !!}
			{!! $errors->first('secret', Form::label('secret', ':message')) !!}
		</div>
	</div>
	<div class="form-group{!! $errors->has('secret_confirmation') ? ' has-error' : '' !!}">
		{!! Form::label('secret_confirmation', trans('user.password_confirmation'), ['class' =>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::password('secret_confirmation', ['class' =>'form-control']) !!}
			{!! $errors->first('secret_confirmation', Form::label('secret_confirmation', ':message')) !!}
		</div>
	</div>
    @endif
    <div class="form-group{!! $errors->has( 'fullname' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'fullname', trans( 'ext.full_name' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
        <div class="col-sm-10">
            {!! Form::text( 'fullname', null, [ 'class' => 'form-control', 'placeholder' => trans( 'ext.full_name-ph' ), 'required' => '' ] ) !!}
            {!! $errors->first( 'fullname', Form::label( 'fullname', ':message' ) ) !!}
        </div>
    </div>
    @if( Auth::user()->isMasterAdmin() )
    <div class="form-group{!! $errors->has( 'organizationId' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'organizationId', trans( 'orgs.org' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
        <div class="col-sm-10">
            {!! Form::select( 'organizationId', [ '' => '' ] + $orgs, ( $currentOrg->isMaster() ? null : $currentOrg->organizationId ), [ 'class' => 'form-control', 'data-placeholder' => trans( 'orgs.select' ) ] ) !!}
            {!! $errors->first( 'organizationId', Form::label( 'organizationId', ':message' ) ) !!}
        </div>
    </div>
    @else
    {!! Form::hidden( 'organizationId', ( isset( $ext ) ? $ext->organizationId : $currentOrg->organizationId ) ) !!}
    @endif
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			{!! Form::submit( trans( 'modal.' . ( ( isset( $ext ) ) ? 'save' : 'create' ) ), [ 'class' => 'btn btn-primary', 'autocomplete' => 'off' ] ) !!}
			<button class="btn btn-link close_popup">{{{ trans( 'modal.cancel' ) }}}</button>
		</div>
	</div>
{!! Form::close() !!}
@stop