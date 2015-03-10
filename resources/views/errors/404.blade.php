@extends('layouts.default')
{{-- Web site Title --}}
@section('title') 404 :: @parent @stop
@section('content')
@include('layouts.page_header', ['page_header' => trans('site.404')])
@stop