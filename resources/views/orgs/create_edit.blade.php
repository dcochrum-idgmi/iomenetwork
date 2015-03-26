@extends( 'layouts.' . ( ( Input::get( 'iframe' ) !== null ) ? 'modal' : 'default' ) )
@section( 'content' )
@include( 'layouts.page_header', [ 'page_header' => trans( 'modal.' . ( ( isset( $org ) ) ? 'update' : 'create' ) ) . ' ' . trans( 'orgs.org' ) ] )

@if( isset( $org ) )
{!! Form::model( $org, [ 'method' => 'PATCH', 'url' => Request::is( 'settings' ) ? sub_route( 'settings' ) : admin_route( 'orgs.update', ['orgs' => $org] ), 'class' => 'form-horizontal' ] ) !!}
@else
{!! Form::open( [ 'url' => admin_route( 'orgs.store' ), 'class' => 'form-horizontal' ] ) !!}
@endif
	<div class="form-group{!! $errors->has( 'organizationName' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'organizationName', trans( 'orgs.name' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::text( 'organizationName', null, [ 'class' => 'form-control', 'placeholder' => trans( 'orgs.name-ph' ) ] ) !!}
			{!! $errors->first( 'organizationName', Form::label( 'organizationName', ':message', [ 'class' => 'error' ] ) ) !!}
		</div>
	</div>
	<div class="form-group{!! $errors->has( 'slug' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'slug', trans( 'orgs.slug' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::text( 'slug', null, [ 'class' => 'form-control', 'placeholder' => trans( 'orgs.slug-ph' ) ] ) !!}
			{!! $errors->first( 'slug', Form::label( 'slug', ':message', [ 'class' => 'error' ] ) ) !!}
		</div>
	</div>
    <div class="form-group{!! $errors->has( 'address' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'address', trans( 'site.address' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
        <div class="col-md-10">
            <div class="form-group">
                <div class="col-xs-12">
                    {!! Form::text( 'address', null, [ 'class' => 'form-control', 'placeholder' => trans( 'site.address-ph' ) ] ) !!}
                    {!! $errors->first( 'address', Form::label( 'address', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    {!! Form::text( 'city', null, [ 'class' => 'form-control', 'placeholder' => trans( 'site.city-ph' ) ] ) !!}
                    {!! $errors->first( 'city', Form::label( 'city', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
                <div class="col-md-3">
	                {!! Form::select( 'state', [ '' => '' ] + $states, null, [ 'class' => 'form-control', 'data-placeholder' => trans( 'site.state-ph' ) ] ) !!}
                    {!! $errors->first( 'state', Form::label( 'state', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::text( 'zipcode', null, [ 'class' => 'form-control', 'placeholder' => trans( 'site.zipcode-ph' ) ] ) !!}
                    {!! $errors->first( 'zipcode', Form::label( 'zipcode', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::select( 'countryId', [ '' => '' ] + $countries, 'US', [ 'class' => 'form-control', 'data-placeholder' => trans( 'site.country' ) ] ) !!}
                    {!! $errors->first( 'countryId', Form::label( 'countryId', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group{!! $errors->has( 'language' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'language', trans( 'site.language' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
        <div class="col-md-10">
            {!! Form::select( 'language', [ 'en_US' => 'English - US' ], null, [ 'class' => 'form-control', 'data-placeholder' => trans( 'site.language' ) ] ) !!}
            {!! $errors->first( 'language', Form::label( 'language', ':message', [ 'class' => 'error' ] ) ) !!}
        </div>
    </div>
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			{!! Form::submit( trans( 'modal.' . ( ( isset( $org ) ) ? 'save' : 'create' ) ), [ 'class' => 'btn btn-primary', 'data-loading-text' => 'Saving...', 'autocomplete' => 'off' ] ) !!}
			{!! link_to( admin_route( 'orgs.index' ), trans( 'modal.cancel' ), [ 'class' => 'btn btn-link close_popup' ] ) !!}
		</div>
	</div>
{!! Form::close() !!}
@stop