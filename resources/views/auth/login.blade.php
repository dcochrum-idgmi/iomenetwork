@extends('layouts.default') {{-- Web site Title --}}
@section('title'){{{ trans('user.login') }}} ::@parent @stop
{{-- Content --}}
@section('content')
    @include('layouts.page_header', ['page_header' => trans('user.login_to_account')])
{!! Form::open(['route' => ['login.post'], 'class' => 'form-horizontal']) !!}
	<fieldset>
        <div class="form-group{!! $errors->has( 'username' ) ? ' has-error' : '' !!}">
            {!! Form::label( 'username', trans( 'users.email' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
            <div class="col-sm-10">
                {!! Form::email( 'username', null, [ 'class' => 'form-control', 'placeholder' => trans( 'users.email-ph' ) ] ) !!}
            </div>
        </div>
        <div class="form-group{!! $errors->has( 'username' ) ? ' has-error' : '' !!}">
            {!! Form::label( 'password', trans( 'users.password' ), [ 'class' => 'col-sm-2 control-label' ] ) !!}
            <div class="col-sm-10">
                {!! Form::password( 'password', [ 'class' => 'form-control' ] ) !!}
            </div>
        </div>
		<div class="form-group">
			<div class="col-sm-10 col-sm-offset-2">
				<div class="checkbox">
					<label>
						{!! Form::checkbox('remember') !!}
						<input type="checkbox" name="remember"> {{ trans('user.remember') }}
					</label>
				</div>
			</div>
		</div>
        <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
                {!! $errors->first( 'username', '<p class="help-block">:message</p>' ) !!}
                {!! Form::submit(trans('user.login'), ['class' => 'btn btn-primary']) !!}
				{!! HTML::linkRoute('resetpw', trans( 'users.forgot' ), [], ['class' => 'btn btn-link']) !!}
			</div>
		</div>
	</fieldset>
</form>

@stop
