@extends('layouts.default')
@section('title') {{{ $ext->name }}} :: @parent @stop {{-- Content --}}
@section('content')
<pre>{!! var_dump($ext->toArray()) !!}</pre>
@stop