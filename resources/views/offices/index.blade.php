@extends('layouts.default')
{{-- Web site Title --}}
@section('title') {!! trans('offices.offices') !!} :: @parent @stop
{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		{!! trans('offices.offices') !!}
		<div class="pull-right">
			<div class="pull-right">
				<a href="{!! route('offices.create') !!}" class="btn btn-sm btn-primary iframe">
					<i class="fa fa-plus-circle"></i>
					{{ trans('modal.new') }}
				</a>
			</div>
		</div>
	</h3>
</div>

<table id="table" class="table table-striped table-hover">
	<thead>
		<tr>
			<th>{!! trans('offices.name') !!}</th>
			<th>{!! trans('offices.slug') !!}</th>
			<th>{!! trans('admin.admins') !!}</th>
			<th>{!! trans('users.active_users') !!}</th>
			<th>{!! trans('users.users') !!}</th>
			<th>{!! trans('ext.extensions') !!}</th>
			<th>{!! trans('admin.created_at') !!}</th>
			<th class="no-sort">{!! trans('admin.action') !!}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
@stop
{{-- Scripts --}}
@section('scripts')
@include('partials.scripts.oTable', ['source' => sub_route('offices.index')])
@stop
@stop
