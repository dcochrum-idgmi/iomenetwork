@extends('layouts.default') {{-- Web site Title --}}
@section('title'){{{ trans('user.login') }}} ::@parent @stop
{{-- Content --}}
@section('content')
    @include('layouts.page_header', ['page_header' => trans('user.login_to_account')])
{!! Form::open(['route' => ['login.post'], 'class' => 'form-horizontal']) !!}
	<fieldset>
		<div class="form-group {{$errors->has('email')?'has-error':''}}">
			<label class="col-md-2 control-label" for="email">
                {{	trans('user.e_mail') }} </label>
			<div class="col-md-10">
				<input class="form-control" tabindex="1"
					placeholder="{{ trans('user.e_mail') }}" type="text"
					name="email" id="email" value="{{ Input::old('email') }}"> <span
					class="help-block">{!!$errors->first('email', '<span
					class="help-block">:message </span>')!!}
				</span>
			</div>
		</div>
		<div class="form-group {{$errors->has('email')?'has-error':''}}">
			<label class="col-md-2 control-label" for="password"> {{
				trans('user.password') }} </label>
			<div class="col-md-10">
				<input class="form-control" tabindex="2"
					placeholder="{{ trans('user.password') }}" type="password"
					name="password" id="password"> <span class="help-block">{!!$errors->first('password',
					'<span class="help-block">:message </span>')!!}
				</span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-10 col-md-offset-2">
				<div class="checkbox">
					<label>
						{!! Form::checkbox('remember') !!}
						<input type="checkbox" name="remember"> {{ trans('user.remember') }}
					</label>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-2 col-md-10">
				{!! Form::submit(trans('user.login'), ['class' => 'btn btn-primary']) !!}
				{!! HTML::linkRoute('resetpw', trans( 'users.forgot' ), [], ['class' => 'btn btn-link']) !!}
			</div>
		</div>
	</fieldset>
</form>

@stop
