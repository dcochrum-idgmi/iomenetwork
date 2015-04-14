@extends('layouts.default')
{{-- Web site Title --}}
@section('title') {!! trans('orgs.orgs') !!} :: @parent @stop
{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		{!! trans('orgs.orgs') !!}
		<div class="pull-right">
			<div class="pull-right">
				<a href="{!! admin_route('orgs.create') !!}" class="btn btn-sm btn-primary iframe">
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
			<th class="search" data-name="organizationName">{!! trans('orgs.name') !!}</th>
			<th class="search" data-name="slug">{!! trans('orgs.slug') !!}</th>
			<th class="no-sort" data-name="numAdmins">{!! trans('admin.admins') !!}</th>
			<th class="no-sort" data-name="numUsers">{!! trans('users.users') !!}</th>
			<th class="no-sort" data-name="numSips">{!! trans('ext.extensions') !!}</th>
			<th data-name="dateEntered">{!! trans('site.created') !!}</th>
			<th data-name="dateModified">{!! trans('site.modified') !!}</th>
			<th class="no-sort" data-name="actions">{!! trans('admin.action') !!}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
@stop
{{-- Scripts --}}
@section('scripts')
@include('partials.scripts.oTable', ['source' => admin_route('orgs.index')])
@stop
@stop
