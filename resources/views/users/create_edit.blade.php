@extends( 'layouts.' . ( ( Input::get( 'iframe' ) !== null ) ? 'modal' : 'default' ) )
@section( 'content' )
@include( 'layouts.page_header', [ 'page_header' => trans( 'users.' . ( isset( $user ) ? 'edit' : 'create' ) ) . ' ' . trans( 'users.user' ) ] )
@if( isset( $user ) )
	@if( Request::is( 'profile' ) )
{!! Form::model( $user, [ 'method' => 'PATCH', 'route' => [ 'profile.update', $currentOffice->slug ], 'class' => 'form-horizontal' ] ) !!}
	@else
{!! Form::model( $user, [ 'method' => 'PATCH', 'route' => [ 'users.update', $currentOffice->slug, $user ], 'class' => 'form-horizontal' ] ) !!}
	@endif
@else
{!! Form::open( [ 'route' => [ 'users.store', $currentOffice->slug ], 'class' => 'form-horizontal' ] ) !!}
@endif
	<div class="form-group{!! $errors->has( 'fname' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'fname', trans( 'users.first_name' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::text( 'fname', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.first_name-ph' ) ] ) !!}
			{!! $errors->first( 'fname', Form::label( 'fname', ':message' ) ) !!}
		</div>
	</div>
    <div class="form-group{!! $errors->has( 'lname' ) ? ' has-error' : '' !!}">
        {!! Form::label( 'lname', trans( 'users.last_name' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
        <div class="col-md-10">
            {!! Form::text( 'lname', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.last_name-ph' ) ] ) !!}
            {!! $errors->first( 'lname', Form::label( 'lname', ':message' ) ) !!}
        </div>
    </div>
	@if( ! isset( $user ) )
	<div class="form-group{!! $errors->has( 'email' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'email', trans( 'users.email' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::email( 'email', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.email-ph' ) ] ) !!}
			{!! $errors->first( 'email', Form::label( 'email', ':message' ) ) !!}
		</div>
	</div>
	@endif
	<div class="form-group{!! $errors->has( 'password' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'password', trans( 'users.' . ( isset( $user ) ? 'new_' : '' ) . 'password' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::password( 'password', [ 'class' => 'form-control' ] ) !!}
			{!! $errors->first( 'password', Form::label( 'password', ':message' ) ) !!}
		</div>
	</div>
	<div class="form-group{!! $errors->has( 'password_confirmation' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'password_confirmation', trans( 'users.password_confirmation' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::password( 'password_confirmation', [ 'class' => 'form-control' ] ) !!}
			{!! $errors->first( 'password_confirmation', Form::label( 'password_confirmation', ':message' ) ) !!}
		</div>
	</div>
	@if( $currentOffice->isVendor() )
	<div class="form-group{!! $errors->has( 'officeId' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'officeId', trans( 'offices.office' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			<select name="officeId" id="officeId" class="form-control" data-placeholder="{{ trans( 'offices.select' ) }}">
				<option></option>
				@foreach( $offices as $val => $text )
				<option value="{{ $val }}"{!! Input::old( 'officeId', ( isset( $user ) ? $user->officeId : null ) ) == $val ? ' selected' : '' !!}>{{ $text }}</option>
				@endforeach
			</select>
{{--			{!! Form::select( 'officeId', array_merge( [ '' => '' ], $offices ), $office_id, [ 'class' => 'form-control', 'data-placeholder' => trans( 'offices.select' ) ] ) !!} --}}
			{!! $errors->first( 'officeId', Form::label( 'officeId', ':message' ) ) !!}
		</div>
	</div>
    @else
    {!! Form::hidden( 'officeId', $currentOffice->id ) !!}
	@endif
{{--	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			<div class="checkbox">
				<label>
					{!! Form::hidden( 'confirmed', '0' ) !!}
					{!! Form::checkbox( 'confirmed', '1' ) !!}
					{!! trans( 'users.activate_user' ) !!}
				</label>
			</div>
		</div>
	</div> --}}
	@if( Auth::user()->isAdmin() )
{{--	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			<div class="checkbox">
				<label>
					@unless( isset( $user ) && Auth::user()->id == $user->id )
					{!! Form::hidden( 'admin', '0' ) !!}
					@endunless
					{!! Form::checkbox( 'admin', '1', null, [ 'id' => 'admin', 'disabled' => ( ( isset( $user ) && Auth::user()->id == $user->id ) ? 'disabled' : null ) ] ) !!}
					{!! trans( 'users.make_admin' ) !!}
				</label>
			</div>
		</div>
	</div> --}}
	<div class="form-group{!! $errors->has( 'authority' ) ? ' has-error' : '' !!}">
		{!! Form::label( 'authority', trans( 'user.role' ), [ 'class' => 'col-md-2 control-label' ] ) !!}
		<div class="col-md-10">
			{!! Form::select( 'authority', $roles, null, [ 'class' => 'form-control', 'data-placeholder' => trans( 'user.role_select' ) ] ) !!}
			{!! $errors->first( 'authority', Form::label( 'authority', ':message' ) ) !!}
		</div>
	</div>
	@endif
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			{!! Form::submit( trans( 'modal.' . ( ( isset( $user ) ) ? 'save' : 'create' ) ), [ 'class' => 'btn btn-primary', 'autocomplete' => 'off' ] ) !!}
			<button class="btn btn-link close_popup">{{{ trans( 'modal.cancel' ) }}}</button>
		</div>
	</div>
{!! Form::close() !!}
@stop