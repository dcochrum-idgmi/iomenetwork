@extends('layouts.default')
@section('title') {{{ $org->organizationName }}} :: @parent @stop {{-- Content --}}
@section('content')
<pre>{!! dd( $currentOrg->toArray(), $org->toArray()) !!}</pre>
@stop