@extends('layouts.default')
@section('title') {{{ $office->officeName }}} :: @parent @stop {{-- Content --}}
@section('content')
<pre>{!! dd( $currentOffice->toArray(), $office->toArray()) !!}</pre>
@stop