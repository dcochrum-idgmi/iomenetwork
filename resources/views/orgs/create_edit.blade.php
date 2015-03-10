@extends( 'layouts.' . ( ( Input::get( 'iframe' ) !== null ) ? 'modal' : 'default' ) )
@section( 'content' )
@include( 'layouts.page_header', [ 'page_header' => trans( 'modal.' . ( ( isset( $org ) ) ? 'update' : 'create' ) ) . ' ' . trans( 'orgs.organization' ) ] )

@if( isset( $org ) )
{!! Form::model( $org, [ 'method' => 'PATCH', 'route' => Request::is( 'settings' ) ? [ 'settings', $org->slug ] : [ 'orgs.update', $org ], 'class' => 'form-horizontal' ] ) !!}
@else
{!! Form::open( [ 'route' => 'orgs.store', 'class' => 'form-horizontal' ] ) !!}
@endif
	<div class="form-group{!! $errors->has( 'officeName' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'officeName', trans( 'orgs.name' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::text( 'officeName', null, [ 'class' => 'form-control', 'placeholder' => trans( 'orgs.name-ph' ) ] ) !!}
			{!! $errors->first( 'officeName', Form::label( 'officeName', ':message', [ 'class' => 'error' ] ) ) !!}
		</div>
	</div>
	<div class="form-group{!! $errors->has( 'slug' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'slug', trans( 'orgs.slug' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::text( 'slug', null, [ 'class' => 'form-control', 'placeholder' => trans( 'orgs.slug-ph' ) ] ) !!}
			{!! $errors->first( 'slug', Form::label( 'slug', ':message', [ 'class' => 'error' ] ) ) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			{!! Form::submit( trans( 'modal.' . ( ( isset( $org ) ) ? 'save' : 'create' ) ), [ 'class' => 'btn btn-primary', 'data-loading-text' => 'Saving...', 'autocomplete' => 'off' ] ) !!}
			<button class="btn btn-link close_popup">{{{ trans( 'modal.cancel' ) }}}</button>
		</div>
	</div>
{!! Form::close() !!}
@stop