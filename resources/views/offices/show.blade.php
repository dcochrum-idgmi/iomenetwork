@extends('layouts.default')
@section('title') {{{ $user->name }}} :: @parent @stop {{-- Content --}}
@section('content')
<pre>{!! var_dump($user->toArray()) !!}</pre>
@stop