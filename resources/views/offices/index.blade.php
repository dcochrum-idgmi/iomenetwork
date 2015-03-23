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
				<a href="{!! route('orgs.create') !!}" class="btn btn-sm btn-primary iframe">
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
			<th data-name="officeName">{!! trans('offices.name') !!}</th>
			<th data-name="officeSlug">{!! trans('offices.slug') !!}</th>
			<th data-name="numAdmins">{!! trans('admin.admins') !!}</th>
			<th data-name="numUsers">{!! trans('users.users') !!}</th>
			<th data-name="numSips">{!! trans('ext.extensions') !!}</th>
			<th data-name="dateEntered">{!! trans('admin.created_at') !!}</th>
			<th class="no-sort" data-name="actions">{!! trans('admin.action') !!}</th>
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
