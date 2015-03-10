@extends('layouts.default')
{{-- Web site Title --}}
@section('title') {!! trans('orgs.organizations') !!} :: @parent @stop
{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		{!! trans('orgs.organizations') !!}
		<div class="pull-right">
			<div class="pull-right">
				<a href="{!! sub_route('orgs.create') !!}" class="btn btn-sm btn-primary iframe">
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
			<th>{!! trans('orgs.name') !!}</th>
			<th>{!! trans('orgs.slug') !!}</th>
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
@include('partials.scripts.oTable', ['source' => sub_route('orgs.index')])
@stop
@stop
