@extends( 'layouts.' . ( ( Input::get( 'iframe' ) !== null ) ? 'modal' : 'default' ) )
@section( 'content' )
@include( 'layouts.page_header', [ 'page_header' => trans( 'users.' . ( isset( $user ) ? 'edit' : 'create' ) ) . ' ' . trans( 'users.user' ) ] )
@if( isset( $user ) )
	@if( Request::is( 'profile' ) )
{!! Form::model( $user, [ 'method' => 'PATCH', 'route' => [ 'profile.update', $currentOrg ], 'class' => 'form-horizontal' ] ) !!}
	@else
{!! Form::model( $user, [ 'method' => 'PATCH', 'route' => [ 'users.update', $currentOrg, $user ], 'class' => 'form-horizontal' ] ) !!}
	@endif
@else
{!! Form::open( [ 'route' => [ 'users.store', $currentOrg ], 'class' => 'form-horizontal' ] ) !!}
@endif
	<div class="form-group{!! $errors->has( 'fname' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'fname', trans( 'users.first_name' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
		<div class="col-sm-10">
			{!! Form::text( 'fname', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.first_name-ph' ), 'required' => '' ] ) !!}
			{!! $errors->first( 'fname', Form::label( 'fname', ':message' ) ) !!}
		</div>
	</div>
    <div class="form-group{!! $errors->has( 'lname' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'lname', trans( 'users.last_name' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
        <div class="col-sm-10">
            {!! Form::text( 'lname', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.last_name-ph' ), 'required' => '' ] ) !!}
            {!! $errors->first( 'lname', Form::label( 'lname', ':message' ) ) !!}
        </div>
    </div>
	<div class="form-group{!! $errors->has( 'username' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'username', trans( 'users.email' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
		<div class="col-sm-10">
			{!! Form::email( 'username', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.email-ph' ), 'required' => '' ] ) !!}
            {!! Form::hidden( 'username_original', ( isset( $user ) ? $user->username : null ) ) !!}
			{!! $errors->first( 'username', Form::label( 'username', ':message' ) ) !!}
		</div>
	</div>
    @if( ! isset( $user ) )
	<div class="form-group{!! $errors->has( 'password' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'password', trans( 'users.password' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
		<div class="col-sm-10">
			{!! Form::password( 'password', [ 'class' => 'form-control' ] ) !!}
			{!! $errors->first( 'password', Form::label( 'password', ':message' ) ) !!}
		</div>
	</div>
	<div class="form-group{!! $errors->has( 'password_confirmation' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'password_confirmation', trans( 'users.password_confirmation' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
		<div class="col-sm-10">
			{!! Form::password( 'password_confirmation', [ 'class' => 'form-control' ] ) !!}
			{!! $errors->first( 'password_confirmation', Form::label( 'password_confirmation', ':message' ) ) !!}
		</div>
	</div>
    @endif
    <div class="form-group{!! $errors->has( 'address' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'address', trans( 'site.address' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
        <div class="col-sm-10">
            <div class="form-group">
                <div class="col-xs-12">
                    {!! Form::text( 'address', null, [ 'class' => 'form-control', 'placeholder' => trans( 'site.address-ph' ) ] ) !!}
                    {!! $errors->first( 'address', Form::label( 'address', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4">
                    {!! Form::text( 'city', null, [ 'class' => 'form-control', 'placeholder' => trans( 'site.city-ph' ) ] ) !!}
                    {!! $errors->first( 'city', Form::label( 'city', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
                <div class="col-sm-3">
                    {!! Form::select( 'state', [ '' => '' ] + $states, null, [ 'class' => 'form-control', 'data-placeholder' => trans( 'site.state-ph' ) ] ) !!}
                    {!! $errors->first( 'state', Form::label( 'state', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text( 'zipcode', null, [ 'class' => 'form-control', 'placeholder' => trans( 'site.zipcode-ph' ) ] ) !!}
                    {!! $errors->first( 'zipcode', Form::label( 'zipcode', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
                <div class="col-sm-3">
                    {!! Form::select( 'countryId', [ '' => '' ] + $countries, 'US', [ 'class' => 'form-control', 'data-placeholder' => trans( 'site.country' ) ] ) !!}
                    {!! $errors->first( 'countryId', Form::label( 'countryId', ':message', [ 'class' => 'error' ] ) ) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group{!! $errors->has( 'language' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'language', trans( 'site.language' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
        <div class="col-sm-10">
            {!! Form::select( 'language', [ 'en_US' => 'English - US' ], null, [ 'class' => 'form-control', 'data-placeholder' => trans( 'site.language' ) ] ) !!}
            {!! $errors->first( 'language', Form::label( 'language', ':message', [ 'class' => 'error' ] ) ) !!}
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
    {!! Form::hidden( 'organizationId', ( isset( $user ) ? $user->organizationId : $currentOrg->organizationId ) ) !!}
	@endif
	@if( Auth::user()->isAdmin() )
    @if( ! isset( $user ) )
	<div class="form-group{!! $errors->has( 'authority' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'authority', trans( 'users.role' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
		<div class="col-sm-10">
			{!! Form::select( 'authority', $roles, $base_role, [ 'class' => 'form-control', 'data-placeholder' => trans( 'users.role_select' ) ] ) !!}
			{!! $errors->first( 'authority', Form::label( 'authority', ':message' ) ) !!}
		</div>
	</div>
    @endif
    <div class="form-group{!! $errors->has( 'enabled' ) ? ' has-error' : '' !!}">
        <div class="col-sm-10 col-sm-offset-2">
            {!! Form::hidden( 'enabled', 0 ) !!}
            <div class="checkbox">
                <label>
                    {!! Form::checkbox( 'enabled', true, ( isset($user) ? $user->enabled : true ) ) !!}
                    {!! trans( 'users.enabled' ) !!}
                </label>
                {!! $errors->first( 'enabled', Form::label( 'enabled', ':message' ) ) !!}
            </div>
        </div>
    </div>
	@endif
	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
			{!! Form::submit( trans( 'modal.' . ( ( isset( $user ) ) ? 'save' : 'create' ) ), [ 'class' => 'btn btn-primary', 'autocomplete' => 'off' ] ) !!}
            {!! link_to( Request::is( 'profile' ) ? sub_route( 'home' ) : sub_route( 'users.index' ), trans( 'modal.cancel' ), [ 'class' => 'btn btn-link close_popup' ] ) !!}
		</div>
	</div>
{!! Form::close() !!}
@stop